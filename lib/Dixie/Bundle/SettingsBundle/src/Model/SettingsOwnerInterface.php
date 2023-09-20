<?php

declare(strict_types=1);

namespace Talav\SettingsBundle\Model;

interface SettingsOwnerInterface
{
    /**
     * @return int|string
     */
    public function getId();
}
