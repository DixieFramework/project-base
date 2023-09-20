<?php

declare(strict_types=1);

namespace Talav\SettingsBundle\Context;

class ScopeContext extends AbstractScopeContext
{
    /**
     * {@inheritdoc}
     */
    public function getScopes(): array
    {
        return [
            ScopeContextInterface::SCOPE_GLOBAL,
            ScopeContextInterface::SCOPE_USER,
        ];
    }
}
