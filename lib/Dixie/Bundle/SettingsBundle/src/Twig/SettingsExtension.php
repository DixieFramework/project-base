<?php

declare(strict_types=1);

namespace Talav\SettingsBundle\Twig;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use InvalidArgumentException;
use Talav\SettingsBundle\Manager\SettingsManagerInterface;
use Talav\SettingsBundle\Trait\SettingManagerAwareTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SettingsExtension extends AbstractExtension
{
    private $requestStack;
    private $settingsManager;

	public function __construct(RequestStack $requestStack, SettingsManagerInterface $settingsManager)
    {
        $this->requestStack    = $requestStack;
	    $this->settingsManager = $settingsManager;
    }

    public function getFunctions(): array
    {
	    return [
		    new TwigFunction('setting', [$this->settingsManager, 'getSelectedFilter'], ['is_safe' => ['all']]),
            new TwigFunction('setting_value', [$this, 'getSettingValue'], ['is_safe' => ['all']]),
            new TwigFunction('settings', [$this->settingsManager, 'getSelectedFilter'], ['is_safe' => ['all']]),
	    ];
    }

	/**
	 * @param string      $name
	 * @param string|null $default
	 *
	 * @return string
	 *
	 * @throws NonUniqueResultException
	 */
	public function getSettingValue(string $name, string $default = null): string
	{
		$request = $this->requestStack->getMainRequest();

		if (null === $request or !$request->request->has($name)) {
			$value = $default;
			$setting = $this->settingsManager->getOneSettingByName($name);

			if (null !== $setting) {
				$value = $setting['value'];
				//$value = $setting->getValue();
			}

			if (null === $setting) {
				throw new InvalidArgumentException('Setting with this name was not found.');
			}

			if (null !== $request) {
				$request->request->set($name, $value);
			}

			return $value;
		} else {
			return $request->request->get($name);
		}
	}
}
