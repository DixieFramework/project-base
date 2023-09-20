<?php

declare(strict_types=1);

namespace Talav\SettingsBundle\Exception;

class InvalidScopeException extends \InvalidArgumentException
{
    public function __construct($scope)
    {
        parent::__construct(sprintf('Scope %s is not supported!.', $scope));
    }
}
