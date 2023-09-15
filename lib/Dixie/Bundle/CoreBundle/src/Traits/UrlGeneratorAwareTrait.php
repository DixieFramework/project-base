<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Traits;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Service\Attribute\Required;

trait UrlGeneratorAwareTrait
{
    protected UrlGeneratorInterface $urlGenerator;

    #[Required]
    public function setUrlGenerator(UrlGeneratorInterface $urlGenerator): void
    {
        $this->urlGenerator = $urlGenerator;
    }
}
