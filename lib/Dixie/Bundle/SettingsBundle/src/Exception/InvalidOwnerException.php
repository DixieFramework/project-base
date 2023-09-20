<?php

declare(strict_types=1);

namespace Talav\SettingsBundle\Exception;

class InvalidOwnerException extends \InvalidArgumentException
{
    public function __construct($scope)
    {
        parent::__construct(sprintf('Provided owner is not supported by scope %s!.', $scope));
    }
}
