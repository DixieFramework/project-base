<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Twig\Extension;

use Talav\CoreBundle\Twig\Runtime\CoreExtensionRuntime;
use Talav\CoreBundle\Utils\ColorUtils;
use Talav\CoreBundle\Utils\Truncator;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

final class CoreExtension extends AbstractExtension
{
    /**
     * Return a list of all filters.
     *
     * @return array
     */
    public function getFilters(): array
    {
        return [
//            new TwigFilter('*ize', [$this, 'inflectorFilter']),
//            new TwigFilter('absolute_url', [$this, 'absoluteUrlFilter']),
//            new TwigFilter('contains', [$this, 'containsFilter']),
//            new TwigFilter('chunk_split', [$this, 'chunkSplitFilter']),
//            new TwigFilter('nicenumber', [$this, 'niceNumberFunc']),
//            new TwigFilter('nicefilesize', [$this, 'niceFilesizeFunc']),
//            new TwigFilter('nicetime', [$this, 'nicetimeFunc']),
//            new TwigFilter('defined', [$this, 'definedDefaultFilter']),
//            new TwigFilter('ends_with', [$this, 'endsWithFilter']),
//            new TwigFilter('fieldName', [$this, 'fieldNameFilter']),
//            new TwigFilter('parent_field', [$this, 'fieldParentFilter']),
//            new TwigFilter('ksort', [$this, 'ksortFilter']),
//            new TwigFilter('ltrim', [$this, 'ltrimFilter']),
//            new TwigFilter('markdown', [$this, 'markdownFunction'], ['needs_context' => true, 'is_safe' => ['html']]),
//            new TwigFilter('md5', [$this, 'md5Filter']),
//            new TwigFilter('base32_encode', [$this, 'base32EncodeFilter']),
//            new TwigFilter('base32_decode', [$this, 'base32DecodeFilter']),
//            new TwigFilter('base64_encode', [$this, 'base64EncodeFilter']),
//            new TwigFilter('base64_decode', [$this, 'base64DecodeFilter']),
//            new TwigFilter('randomize', [$this, 'randomizeFilter']),
//            new TwigFilter('modulus', [$this, 'modulusFilter']),
//            new TwigFilter('rtrim', [$this, 'rtrimFilter']),
//            new TwigFilter('pad', [$this, 'padFilter']),
//            new TwigFilter('regex_replace', [$this, 'regexReplace']),
//            new TwigFilter('safe_email', [$this, 'safeEmailFilter'], ['is_safe' => ['html']]),
            new TwigFilter('safe_truncate', [$this, 'safeTruncate']),
            new TwigFilter('safe_truncate_html', [$this, 'safeTruncateHTML']),
//            new TwigFilter('sort_by_key', [$this, 'sortByKeyFilter']),
//            new TwigFilter('starts_with', [$this, 'startsWithFilter']),
            new TwigFilter('truncate', [$this, 'truncate']),
            new TwigFilter('truncate_html', [$this, 'truncateHtml']),
//            new TwigFilter('json_decode', [$this, 'jsonDecodeFilter']),
//            new TwigFilter('array_unique', 'array_unique'),
//            new TwigFilter('basename', 'basename'),
//            new TwigFilter('dirname', 'dirname'),
//            new TwigFilter('print_r', [$this, 'print_r']),
//            new TwigFilter('yaml_encode', [$this, 'yamlEncodeFilter']),
//            new TwigFilter('yaml_decode', [$this, 'yamlDecodeFilter']),
//            new TwigFilter('nicecron', [$this, 'niceCronFilter']),
//            new TwigFilter('replace_last', [$this, 'replaceLastFilter']),
//
//            // Translations
//            new TwigFilter('t', [$this, 'translate'], ['needs_environment' => true]),
//            new TwigFilter('tl', [$this, 'translateLanguage']),
//            new TwigFilter('ta', [$this, 'translateArray']),
//
//            // Casting values
//            new TwigFilter('string', [$this, 'stringFilter']),
//            new TwigFilter('int', [$this, 'intFilter'], ['is_safe' => ['all']]),
//            new TwigFilter('bool', [$this, 'boolFilter']),
//            new TwigFilter('float', [$this, 'floatFilter'], ['is_safe' => ['all']]),
//            new TwigFilter('array', [$this, 'arrayFilter']),
//            new TwigFilter('yaml', [$this, 'yamlFilter']),
//
//            // Object Types
//            new TwigFilter('get_type', [$this, 'getTypeFunc']),
//            new TwigFilter('of_type', [$this, 'ofTypeFunc']),
//
//            // PHP methods
//            new TwigFilter('count', 'count'),
//            new TwigFilter('array_diff', 'array_diff'),
//
//            // Security fixes
//            new TwigFilter('filter', [$this, 'filterFunc'], ['needs_environment' => true]),
//            new TwigFilter('map', [$this, 'mapFunc'], ['needs_environment' => true]),
//            new TwigFilter('reduce', [$this, 'reduceFunc'], ['needs_environment' => true]),

              // design
              new TwigFilter('colorize', [$this, 'colorize']),
              new TwigFilter('font_contrast', [$this, 'calculateFontContrastColor']),
              new TwigFilter('default_color', [$this, 'defaultColor']),
        ];
    }
    public function getFunctions(): array
    {
        return [
            new TwigFunction('icon', [CoreExtensionRuntime::class, 'displayIcon']),
        ];
    }

    /**
     * Truncate text by number of characters but can cut off words.
     *
     * @param string $string
     * @param int $limit Max number of characters.
     * @param bool $up_to_break truncate up to breakpoint after char count
     * @param string $break Break point.
     * @param string $pad Appended padding to the end of the string.
     * @return string
     */
    public static function truncate($string, $limit = 150, $up_to_break = false, $break = ' ', $pad = '&hellip;')
    {
        // return with no change if string is shorter than $limit
        if (mb_strlen($string) <= $limit) {
            return $string;
        }

        // is $break present between $limit and the end of the string?
        if ($up_to_break && false !== ($breakpoint = mb_strpos($string, $break, $limit))) {
            if ($breakpoint < mb_strlen($string) - 1) {
                $string = mb_substr($string, 0, $breakpoint) . $pad;
            }
        } else {
            $string = mb_substr($string, 0, $limit) . $pad;
        }

        return $string;
    }

    /**
     * Truncate text by number of characters in a "word-safe" manor.
     *
     * @param string $string
     * @param int $limit
     * @return string
     */
    public static function safeTruncate($string, $limit = 150)
    {
        return static::truncate($string, $limit, true);
    }


    /**
     * Truncate HTML by number of characters. not "word-safe"!
     *
     * @param string $text
     * @param int $length in characters
     * @param string $ellipsis
     * @return string
     */
    public static function truncateHtml($text, $length = 100, $ellipsis = '...')
    {
        return Truncator::truncateLetters($text, $length, $ellipsis);
    }

    /**
     * Truncate HTML by number of characters in a "word-safe" manor.
     *
     * @param string $text
     * @param int $length in words
     * @param string $ellipsis
     * @return string
     */
    public static function safeTruncateHtml($text, $length = 25, $ellipsis = '...')
    {
        return Truncator::truncateWords($text, $length, $ellipsis);
    }

    public function colorize(?string $color, ?string $identifier = null, ?string $fallback = null): string
    {
        if ($color !== null) {
            return $color;
        }

        if ($this->randomColors === null) {
            $this->randomColors = false;//$this->configuration->isThemeRandomColors();
        }

        if ($this->randomColors) {
            return (new ColorUtils())->getRandom($identifier);
        }

        if ($fallback !== null) {
            return $fallback;
        }

        return ColorUtils::DEFAULT_COLOR;
    }

    public function calculateFontContrastColor(string $color): string
    {
        return (new ColorUtils())->getFontContrastColor($color);
    }

    public function defaultColor(?string $color = null): string
    {
        return $color ?? ColorUtils::DEFAULT_COLOR;
    }
}
