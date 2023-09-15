<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Traits;

use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Service\Attribute\Required;

trait SecurityAwareTrait
{
    protected Security $security;

    #[Required]
    public function setSecurity(Security $security): void
    {
        $this->security = $security;
    }
}
