<?php

declare(strict_types=1);

namespace Talav\WebBundle\Engine\Asset;

use DateTime;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\RouterInterface;
use Zikula\Bundle\CoreBundle\HttpKernel\ZikulaHttpKernelInterface;
use Talav\WebBundle\Engine\AssetBag;

use function Symfony\Component\String\s;

class Merger implements MergerInterface
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var ZikulaHttpKernelInterface
     */
    private $kernel;

    /**
     * @var string
     */
    private $rootDir;

    /**
     * @var integer
     */
    private $lifetime;

    /**
     * @var boolean
     */
    private $minify;

    /**
     * @var boolean
     */
    private $compress;

    /**
     * @var string[]
     */
    private $skipFiles;

    public function __construct(
        RouterInterface $router,
        KernelInterface $kernel,
        string $lifetime = '1 day',
        bool $minify = false,
        bool $compress = false,
        array $skipFiles = []
    ) {
        $this->router = $router;
        $this->kernel = $kernel;
        $publicDir = realpath($kernel->getProjectDir() . '/public');
        $basePath = $router->getContext()->getBaseUrl();
        $this->rootDir = str_replace($basePath, '', $publicDir);
        $this->lifetime = abs((new DateTime($lifetime))->getTimestamp() - (new DateTime())->getTimestamp());
        $this->minify = $minify;
        $this->compress = $compress;

        $this->skipFiles = [];
        foreach ($skipFiles as $path) {
            $this->skipFiles[] = $basePath . $path;
        }
    }

    public function merge(array $assets, $type = 'js'): array
    {
        if (!in_array($type, ['js', 'css'])) {
            return [];
        }

        $preCachedFiles = [];
        $cachedFiles = [];
        $outputFiles = [];
        $postCachedFiles = [];
        foreach ($assets as $asset => $weight) {
            $path = realpath($this->rootDir . $asset);
            // skip remote files and specific unwanted ones from combining
            if (
                false !== $path
                && is_file($path)
                && !in_array($asset, $this->skipFiles)
                && null === s($asset)->indexOf('/public/bootswatch')
                && !in_array($weight, [AssetBag::WEIGHT_ROUTER_JS, AssetBag::WEIGHT_ROUTES_JS])
            ) {
                $cachedFiles[] = $path;
            } elseif (0 > $weight) {
                $preCachedFiles[$asset] = $weight;
            } elseif (AssetBag::WEIGHT_DEFAULT < $weight) {
                $postCachedFiles[$asset] = $weight;
            } else {
                $outputFiles[$asset] = $weight;
            }
        }
        $cacheService = new FilesystemAdapter(
            'combined_assets',
            $this->lifetime,
            $this->kernel->getCacheDir() . '/assets/' . $type
        );
        $key = md5(serialize($assets)) . (int) $this->minify . (int) $this->compress . $this->lifetime . '.combined.' . $type;
        $cacheService->get($key, function () use ($cachedFiles, $type) {
            $data = [];
            foreach ($cachedFiles as $k => $file) {
                $this->readFile($data, $file, $type);
                // avoid exposure of absolute server path
                $pathParts = explode($this->rootDir, $file);
                $cachedFiles[$k] = end($pathParts);
            }
            $now = new DateTime();
            array_unshift($data, sprintf("/* --- Combined file written: %s */\n\n", $now->format('c')));
            array_unshift($data, sprintf("/* --- Combined files:\n%s\n*/\n\n", implode("\n", $cachedFiles)));
            $data = implode('', $data);
            if ('css' === $type && $this->minify) {
                $data = $this->minify($data);
            }

            return $data;
        });

        $route = $this->router->generate('zikulathememodule_combinedasset_asset', ['type' => $type, 'key' => $key]);
        $outputFiles[$route] = AssetBag::WEIGHT_DEFAULT;

        $outputFiles = array_merge($preCachedFiles, $outputFiles, $postCachedFiles);

        return $outputFiles;
    }

    /**
     * Read a file and add its contents to the $contents array.
     * This function includes the content of all "@import" statements (recursive).
     */
    private function readFile(array &$contents, string $file, string $ext): void
    {
        if (!file_exists($file)) {
            return;
        }
        $source = fopen($file, 'r');
        if (false === $source) {
            return;
        }

        // avoid exposure of absolute server path
        $pathParts = explode($this->rootDir, $file);
        $relativePath = end($pathParts);
        $contents[] = "/* --- Source file: {$relativePath} */\n\n";
        $inMultilineComment = false;
        $importsAllowed = true;
        $wasCommentHack = false;
        while (!feof($source)) {
            if ('css' === $ext) {
                $line = fgets($source, 4096);
                $lineParse = s(false !== $line ? trim($line) : '')->toString();
                $lineParseLength = mb_strlen($lineParse, 'UTF-8');
                $newLine = '';
                // parse line char by char
                for ($i = 0; $i < $lineParseLength; $i++) {
                    $char = $lineParse[$i];
                    $nextchar = $i < ($lineParseLength - 1) ? $lineParse[$i + 1] : '';
                    if (!$inMultilineComment && '/' === $char && '*' === $nextchar) {
                        // a multiline comment starts here
                        $inMultilineComment = true;
                        $wasCommentHack = false;
                        $newLine .= $char . $nextchar;
                        $i++;
                    } elseif ($inMultilineComment && '*' === $char && '/' === $nextchar) {
                        // a multiline comment stops here
                        $inMultilineComment = false;
                        $newLine .= $char . $nextchar;
                        if ('/*\*//*/' === s($lineParse)->slice($i - 3, 8)) {
                            $wasCommentHack = true;
                            $i += 3; // move to end of hack process hack as it where
                            $newLine .= '/*/'; // fix hack comment because we lost some chars with $i += 3
                        }
                        $i++;
                    } elseif ($importsAllowed && '@' === $char && '@import' === s($lineParse)->slice($i, 7)) {
                        // an @import starts here
                        $lineParseRest = s($lineParse)->slice($i + 7)->trim();
                        if ($lineParseRest->ignoreCase()->startsWith('url')) {
                            // the @import uses url to specify the path
                            $posEnd = s($lineParse)->indexOf(';', $i);
                            $charsEnd = s($lineParse)->slice($posEnd - 1, 2);
                            if (');' === $charsEnd) {
                                // used url() without media
                                $start = $lineParseRest->indexOf('(') + 1;
                                $end = $lineParseRest->indexOf(')');
                                $url = $lineParseRest->slice($start, $end - $start);
                                $url = $url->trimStart('"')->trimStart('"');
                                // fix url
                                if ($url->startsWith('http')) {
                                    $newLine .= '@import url("' . $url . '");';
                                } else {
                                    $url = dirname($file) . '/' . $url;
                                    if (!$wasCommentHack) {
                                        // clear buffer
                                        $contents[] = $newLine;
                                        $newLine = '';
                                        // process include
                                        $this->readFile($contents, $url, $ext);
                                    } else {
                                        $newLine .= '@import url("' . $url . '");';
                                    }
                                }
                                // skip @import statement
                                $i += $posEnd - $i;
                            } else {
                                // @import contains media type so we can't include its contents.
                                // We need to fix the url instead.
                                $start = $lineParseRest->indexOf('(') + 1;
                                $end = $lineParseRest->indexOf(')');
                                $url = $lineParseRest->slice($start, $end - $start);
                                $url = $url->trimStart('"')->trimStart('"');
                                // fix url
                                $url = dirname($file) . '/' . $url;
                                // readd @import with fixed url
                                $newLine .= '@import url("' . $url . '")' . $lineParseRest->slice($end + 1, $lineParseRest->indexOf(';') - $end - 1) . ';';
                                // skip @import statement
                                $i += $posEnd - $i;
                            }
                        } elseif ($lineParseRest->startsWith('"') || $lineParseRest->startsWith('\'')) {
                            // the @import uses an normal string to specify the path
                            $posEnd = $lineParseRest->indexOf(';');
                            $url = $lineParseRest->slice(1, $posEnd - 2);
                            $posEnd = s($lineParse)->indexOf(';', $i);
                            // fix url
                            $url = dirname($file) . '/' . $url;
                            if (!$wasCommentHack) {
                                // clear buffer
                                $contents[] = $newLine;
                                $newLine = '';
                                // process include
                                self::readFile($contents, $url, $ext);
                            } else {
                                $newLine .= '@import url("' . $url . '");';
                            }
                            // skip @import statement
                            $i += $posEnd - $i;
                        }
                    } elseif (!$inMultilineComment && ' ' !== $char && "\n" !== $char && "\r\n" !== $char && "\r" !== $char) {
                        // css rule found -> stop processing of @import statements
                        $importsAllowed = false;
                        $newLine .= $char;
                    } else {
                        $newLine .= $char;
                    }
                }
                // fix other paths after @import processing
                if (!$importsAllowed) {
                    $relativePath = str_replace(realpath($this->rootDir), '', $file);
                    $newLine = $this->cssFixPath($newLine, explode('/', dirname($relativePath)));
                }
                $contents[] = $newLine;
            } else {
                $line = fgets($source, 4096);
                if (false === $line || 0 === mb_strpos($line, '//# sourceMappingURL=')) {
                    continue;
                }
                $contents[] = $line;
            }
        }
        fclose($source);
        if ('js' === $ext) {
            $contents[] = "\n;\n";
        } else {
            $contents[] = "\n\n";
        }
    }

    /**
     * Fix paths in CSS files.
     */
    private function cssFixPath(string $line, array $filePathSegments = []): string
    {
        $regexpurl = '/url\([\'"]?([\.\/]*)(.*?)[\'"]?\)/i';
        if (false === mb_strpos($line, 'url')) {
            return $line;
        }

        preg_match_all($regexpurl, $line, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            if (0 !== mb_strpos($match[1], '/') && 0 !== mb_strpos($match[2], 'http://') && 0 !== mb_strpos($match[2], 'https://')) {
                $depth = mb_substr_count($match[1], '../') * -1;
                $pathSegments = $depth < 0 ? array_slice($filePathSegments, 0, $depth) : $filePathSegments;
                $path = implode('/', $pathSegments) . '/';
                $line = str_replace($match[0], "url('{$path}{$match[2]}')", $line);
            }
        }

        return $line;
    }

    /**
     * Remove comments, whitespace and spaces from css files.
     */
    private function minify(string $input): string
    {
        // Remove comments.
        $content = trim(preg_replace('/\/\*.*?\*\//s', '', $input));
        // Compress whitespace.
        $content = preg_replace('/\s+/', ' ', $content);
        // Additional whitespace optimisation -- spaces around certain tokens is not required by CSS
        $content = preg_replace('/\s*(;|\{|\}|:|,)\s*/', '\1', $content);

        return $content;
    }
}
