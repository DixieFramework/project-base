<?php

declare(strict_types=1);

namespace Talav\WebBundle\Engine\Asset;

use Talav\WebBundle\Engine\AssetBag;

/**
 * Class CssResolver
 *
 * This class compiles all css page assets into proper html code for inclusion into a page header
 */
class CssResolver implements ResolverInterface
{
    /**
     * @var AssetBag
     */
    private $bag;

    /**
     * @var MergerInterface
     */
    private $merger;

    /**
     * @var bool
     */
    private $combine;

    public function __construct(
        string $env,
        AssetBag $bag,
        MergerInterface $merger,
        bool $combine = false
    ) {
        $this->bag = $bag;
        $this->merger = $merger;
        $this->combine = ('prod' === $env) && $combine;
    }

    public function compile(): string
    {
        $assets = $this->bag->allWithWeight();
        if ($this->combine) {
            $assets = $this->merger->merge($assets, 'css');
        }
        $headers = '';
        foreach ($assets as $asset => $weight) {
            $headers .= '<link rel="stylesheet" href="' . $asset . '" />' . "\n";
        }

        return $headers;
    }

    public function getBag(): AssetBag
    {
        return $this->bag;
    }
}
