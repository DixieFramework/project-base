<?php

declare(strict_types=1);

namespace Talav\WebBundle\Engine\Asset;

use function Symfony\Component\String\s;
use Talav\WebBundle\Engine\AssetBag;

/**
 * Class JsResolver
 *
 * This class compiles all js page assets into proper html code for inclusion into a page header or footer
 */
class JsResolver implements ResolverInterface
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
        $this->validateBag();
        $assets = $this->bag->allWithWeight();
        if ($this->combine) {
            $assets = $this->merger->merge($assets);
        }
        $scripts = '';
        foreach ($assets as $asset => $weight) {
            $scripts .= '<script src="' . $asset . '"></script>' . "\n";
        }

        return $scripts;
    }

    public function getBag(): AssetBag
    {
        return $this->bag;
    }

    private function validateBag(): void
    {
        foreach ($this->getBag()->all() as $source) {
            // if already there, add jquery-ui again to force weight setting is correct
            // jQueryUI must be loaded before Bootstrap, refs #3912
            if (
                s($source)->endsWith('jquery-ui.min.js')
                || s($source)->endsWith('jquery-ui.js')
            ) {
                $this->bag->add([$source => AssetBag::WEIGHT_JQUERY_UI]);
            }
        }
    }
}
