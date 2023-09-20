<?php

declare(strict_types=1);

namespace Talav\SettingsBundle\Provider;

interface SettingsProviderInterface
{
    /**
     * @return array
     */
    public function getSettings(): array;

    /**
     * @return bool
     */
    public function supports(): bool;
}
