<?php

declare(strict_types=1);

namespace Talav\CoreBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class TalavCoreBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $this->includeHelpers();

        parent::build($container);
    }

    /**
     * Includes some helper files.
     */
    private function includeHelpers(): void
    {
        require __DIR__.'/Resources/functions/misc.php';
    }

}
