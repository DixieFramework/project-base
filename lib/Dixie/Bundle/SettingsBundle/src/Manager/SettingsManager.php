<?php

declare(strict_types=1);

namespace Talav\SettingsBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Talav\Component\Resource\Manager\ResourceManager;
use Talav\SettingsBundle\Context\ScopeContextInterface;
use Talav\SettingsBundle\Exception\InvalidOwnerException;
use Talav\SettingsBundle\Exception\InvalidScopeException;
use Talav\SettingsBundle\Model\SettingsInterface;
use Talav\SettingsBundle\Model\SettingsOwnerInterface;
use Talav\SettingsBundle\Repository\SettingsRepositoryInterface;
use Talav\SettingsBundle\Provider\SettingsProviderInterface;
use Talav\Component\Resource\Factory\FactoryInterface;

class SettingsManager extends ResourceManager implements SettingsManagerInterface
{
    protected $settingsProvider;

    protected $settingsRepository;

    protected $scopeContext;

    protected $internalCache = [];

    public function __construct(
	    protected string $className,
        protected EntityManagerInterface $em,
	    protected FactoryInterface $settingsFactory,
	    #[Autowire('@talav_settings.provider.configuration_settings')] SettingsProviderInterface $settingsProvider,
        SettingsRepositoryInterface $settingsRepository,
        ScopeContextInterface $scopeContext
    ) {
        parent::__construct($className, $em, $settingsFactory);
        $this->settingsProvider = $settingsProvider;
        $this->settingsRepository = $settingsRepository;
        $this->scopeContext = $scopeContext;
    }

    public function get(string $name, $scope = ScopeContextInterface::SCOPE_GLOBAL, SettingsOwnerInterface $owner = null, $default = null)
    {
        $keyElements = [$name, $scope, $default, $this->scopeContext->getScopes()];
        if (null !== $owner) {
            $keyElements[] = $owner->getId();
        }
        $cacheKey = md5(json_encode($keyElements, JSON_THROW_ON_ERROR, 512));
        if (isset($this->internalCache[$cacheKey])) {
            return $this->internalCache[$cacheKey];
        }

        // Allow scope discovery from configuration
        if (null !== $scope) {
            $this->validateScopeAndOwner($scope, $owner);
        }

        $defaultSetting = $this->getFromConfiguration($scope, $name);

        /** @var SettingsInterface $setting */
        $setting = $this->getSettingFromRepository($name, $defaultSetting['scope'], $owner);

        if (null !== $setting) {
            $value = $this->decodeValue($defaultSetting['type'], $setting->getValue());
            $this->internalCache[$cacheKey] = $value;

            return $value;
        }

        if (null !== $default) {
            $this->internalCache[$cacheKey] = $default;

            return $default;
        }

        $value = $this->decodeValue($defaultSetting['type'], $defaultSetting['value']);
        $this->internalCache[$cacheKey] = $value;

        return $value;
    }

    public function all(): array
    {
        $cacheKey = md5(json_encode(['all', $this->scopeContext], JSON_THROW_ON_ERROR, 512));
        if (isset($this->internalCache[$cacheKey])) {
            return $this->internalCache[$cacheKey];
        }

        $settings = $this->getFromConfiguration();
        $settings = $this->processSettings($settings, $this->getSettingsFromRepository());
        $this->internalCache[$cacheKey] = $settings;

        return $settings;
    }

    public function getByScopeAndOwner(string $scope, SettingsOwnerInterface $settingsOwner): array
    {
        $settings = $this->getFromConfiguration($scope);
        $persistedSettings = $this->settingsRepository->findByScopeAndOwner($scope, $settingsOwner)->getQuery()->getResult();

        return $this->processSettings($settings, $persistedSettings);
    }

    public function set(string $name, $value, $scope = ScopeContextInterface::SCOPE_GLOBAL, SettingsOwnerInterface $owner = null)
    {
        $this->internalCache = [];
        $this->validateScopeAndOwner($scope, $owner);
        $defaultSetting = $this->getFromConfiguration($scope, $name);

        /** @var SettingsInterface $setting */
        $setting = $this->getSettingFromRepository($name, $scope, $owner);
        if (null === $setting) {
            /** @var SettingsInterface $setting */
            $setting = $this->settingsFactory->create();
            $setting->setName($name);
            $setting->setScope($scope);

            if (null !== $owner) {
                $setting->setOwner($owner->getId());
            }
            $this->em->persist($setting);
        } else {
            $setting->setUpdatedAt(new \DateTime());
        }

        $setting->setValue($this->encodeValue($defaultSetting['type'], $value));
        $this->em->flush();

        return $setting;
    }

    public function getOneSettingByName(string $name): ?array
    {
        foreach ($this->all() as $setting) {
            if ($setting['name'] === $name) {
                return $setting;
            }
        }

        return null;
    }

    public function clear(string $name, $scope = ScopeContextInterface::SCOPE_GLOBAL, SettingsOwnerInterface $owner = null): bool
    {
        $this->internalCache = [];
        $this->validateScopeAndOwner($scope, $owner);

        $setting = $this->getSettingFromRepository($name, $scope, $owner);
        if (null !== $setting) {
            $this->em->remove($setting);

            return true;
        }

        return false;
    }

    public function clearAllByScope(string $scope = ScopeContextInterface::SCOPE_GLOBAL): void
    {
        $this->validateScope($scope);

        $this->settingsRepository->removeAllByScope($scope);
    }

    protected function validateScope(string $scope): void
    {
        if (!\in_array($scope, $this->scopeContext->getScopes(), true)) {
            throw new InvalidScopeException($scope);
        }
    }

    protected function validateScopeAndOwner(string $scope, $owner = null)
    {
        $this->validateScope($scope);

        if (ScopeContextInterface::SCOPE_GLOBAL !== $scope && null === $owner) {
            throw new InvalidOwnerException($scope);
        }
    }

    private function processSettings(array $settings = [], array $persistedSettings = []): array
    {
        $convertedSettings = [];

        foreach ($settings as $key => $setting) {
            $setting['name'] = $key;
            $convertedSettings[] = $setting;
        }

        foreach ($persistedSettings as $key => $setting) {
            foreach ($convertedSettings as $keyConverted => $convertedSetting) {
                if (isset($convertedSetting['name']) && $convertedSetting['name'] === $setting->getName()) {
                    $convertedSetting['value'] = $this->decodeValue(
                        $convertedSetting['type'],
                        $setting->getValue()
                    );

                    $convertedSettings[$keyConverted] = $convertedSetting;
                }
            }
        }

        return $convertedSettings;
    }

    private function getFromConfiguration(string $scope = null, $name = null)
    {
        $settings = [];
        $settingsConfig = $this->settingsProvider->getSettings();

        if (null !== $name && array_key_exists($name, $settingsConfig)) {
            $setting = $settingsConfig[$name];
            if (null === $scope || $setting['scope'] === $scope) {
                return $settings[$name] = $setting;
            }

            throw new InvalidScopeException($scope);
        }

        if (null !== $name) {
            throw new Exception('There is no setting with this name.');
        }

        foreach ($settingsConfig as $key => $setting) {
            if (null === $scope || $setting['scope'] === $scope) {
                $setting['value'] = $this->decodeValue($setting['type'], $setting['value']);
                $settings[$key] = $setting;
            }
        }

        return $settings;
    }

    /**
     * @return array|mixed
     */
    private function getSettingsFromRepository()
    {
        return $this->settingsRepository->findAllByScopeAndOwner($this->scopeContext)->getQuery()->getResult();
    }

    private function getSettingFromRepository(string $name, string $scope, SettingsOwnerInterface $owner = null): ?SettingsInterface
    {
        return $this->settingsRepository
            ->findOneByNameAndScopeAndOwner($name, $scope, $owner)
            ->getQuery()
            ->getOneOrNullResult();
    }

    private function encodeValue(string $settingType, $value)
    {
        if ('string' === $settingType) {
            return (string) $value;
        }

        if (($actualType = gettype($value)) !== $settingType) {
            throw new Exception(sprintf('Value type should be "%s" not "%s"', $settingType, $actualType));
        }

        if ('array' === $settingType) {
            return json_encode($value);
        }

        return $value;
    }

    private function decodeValue(string $settingType, $value)
    {
        if ('array' === $settingType) {
            return json_decode($value, true);
        }

        if ('boolean' === $settingType) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        if ('integer' === $settingType) {
            return filter_var($value, FILTER_VALIDATE_INT);
        }

        return $value;
    }
}
