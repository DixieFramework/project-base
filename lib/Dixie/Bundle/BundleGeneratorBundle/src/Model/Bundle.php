<?php

declare(strict_types=1);

namespace Talav\BundleGeneratorBundle\Model;

class Bundle extends BaseBundle
{
    public function shouldGenerateDependencyInjectionDirectory()
    {
        return true;
    }
}
