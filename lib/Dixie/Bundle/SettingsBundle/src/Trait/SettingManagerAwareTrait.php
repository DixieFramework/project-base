<?php

declare(strict_types=1);

namespace Talav\SettingsBundle\Trait;

use Talav\SettingsBundle\Manager\SettingsManagerInterface;
use Symfony\Contracts\Service\Attribute\Required;

trait SettingManagerAwareTrait
{
    /**
     * @var SettingsManagerInterface
     */
    protected SettingsManagerInterface $settingManager;

    /**
     * @return SettingsManagerInterface
     */
    public function getSettingManager(): SettingsManagerInterface
    {
        return $this->settingManager;
    }

	#[Required]
	public function setSettingManager(SettingsManagerInterface $settingManager): void
    {
        $this->settingManager = $settingManager;
    }
}
