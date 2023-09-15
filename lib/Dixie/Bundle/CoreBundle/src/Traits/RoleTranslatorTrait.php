<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Traits;

use Talav\CoreBundle\Interfaces\RoleInterface;

/**
 * Trait to translate role.
 */
trait RoleTranslatorTrait
{
    use TranslatorTrait;

    /**
     * Translate the given role.
     */
    public function translateRole(RoleInterface|string $role): string
    {
        if ($role instanceof RoleInterface) {
            $role = $role->getRole();
        }
        $id = \strtolower(\str_ireplace('role_', 'user.roles.', $role));

        return $this->trans($id);
    }
}
