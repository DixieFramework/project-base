<?php

declare(strict_types=1);

namespace Talav\PermissionBundle\Enum;

/**
 * Class Role
 */
#[\PHPUnit\Framework\Attributes\CodeCoverageIgnore]
final class Role
{
    /**
     * Manage all rates and execute any operation against rates
     */
    const MANAGE_RATE = 'ROLE_EXCHANGE_RATE_MANAGE';

    /**
     * See rates
     */
    const VIEW_RATE = 'ROLE_EXCHANGE_RATE_VIEW';

    /**
     * Create rate
     */
    const CREATE_RATE = 'ROLE_EXCHANGE_RATE_CREATE';

    /**
     * Edit rate
     */
    const EDIT_RATE = 'ROLE_EXCHANGE_RATE_EDIT';

    /**
     * Delete rate
     */
    const DELETE_RATE = 'ROLE_EXCHANGE_RATE_DELETE';

    private function __construct()
    {
        // noop
    }
}
