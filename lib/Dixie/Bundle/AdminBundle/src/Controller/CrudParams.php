<?php

declare(strict_types=1);

namespace Talav\AdminBundle\Controller;

use Talav\CoreBundle\Entity\Interfaces\EntityInterface;

class CrudParams
{
    public function __construct(
        public readonly CrudAction $action = CrudAction::READ,
        public readonly ?EntityInterface $item = null,
        public readonly ?string $formClass = null,
        public readonly ?string $view = null,
        public readonly ?string $redirectUrl = null,
        public readonly bool $hasIndex = true,
        public readonly bool $hasShow = false,
        public readonly bool $overrideView = false
    ) {
    }
}
