<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Traits;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Contracts\Service\Attribute\Required;

trait KernelAwareTrait
{
    protected KernelInterface $kernel;

    #[Required]
    public function setKernel(KernelInterface $kernel): void
    {
        $this->kernel = $kernel;
    }
}
