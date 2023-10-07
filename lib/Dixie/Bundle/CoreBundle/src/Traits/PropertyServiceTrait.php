<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Traits;

use Talav\CoreBundle\Entity\AbstractProperty;
use Talav\CoreBundle\Enums\EntityAction;
use Talav\CoreBundle\Utils\StringUtils;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\Attribute\Required;
use Symfony\Contracts\Service\ServiceSubscriberTrait;

/**
 * Trait for class implementing the {@link \Talav\CoreBundle\Interfaces\PropertyServiceInterface PropertyServiceInterface} interface.
 *
 * @psalm-require-implements \App\Interfaces\PropertyServiceInterface
 */
trait PropertyServiceTrait
{
    use CacheAwareTrait {
        CacheAwareTrait::clearCache as private doClearCache;
        CacheAwareTrait::saveDeferredCacheValue as private doSaveDeferredCacheValue;
    }
    use LoggerAwareTrait;
    use ServiceSubscriberTrait {
        ServiceSubscriberTrait::setContainer as doSetContainer;
    }
    use TranslatorAwareTrait;

    /**
     * Clear this cache.
     */
    public function clearCache(): bool
    {
        if (!$this->doClearCache()) {
            $this->logWarning($this->trans('application_service.clear_error'));

            return false;
        }

        return true;
    }

    public function isActionEdit(): bool
    {
        return EntityAction::EDIT === $this->getEditAction();
    }

    public function isActionNone(): bool
    {
        return EntityAction::NONE === $this->getEditAction();
    }

    public function isActionShow(): bool
    {
        return EntityAction::SHOW === $this->getEditAction();
    }

    public function isDarkNavigation(): bool
    {
        return $this->getPropertyBoolean(self::P_DARK_NAVIGATION, true);
    }

    public function saveDeferredCacheValue(string $key, mixed $value, int|\DateInterval $time = null): bool
    {
        if (!$this->doSaveDeferredCacheValue($key, $value, $time)) {
            $this->logWarning($this->trans('application_service.deferred_error', ['%key%' => $key]));

            return false;
        }

        return true;
    }

    /**
     * Override to update cached values.
     */
    #[Required]
    public function setContainer(ContainerInterface $container): ?ContainerInterface
    {
        $result = $this->doSetContainer($container);

        try {
            if (!$this->getPropertyBoolean(self::P_CACHE_SAVED)) {
                $this->updateAdapter();
            }
        } catch (\Exception $e) {
            $this->logException($e);
        }

        return $result;
    }

    /**
     * Save the given properties to the database and to the cache.
     *
     * @param array<string, mixed> $properties the properties to set
     */
    abstract public function setProperties(array $properties): static;

    /**
     * Sets a single property value.
     */
    public function setProperty(string $name, mixed $value): self
    {
        return $this->setProperties([$name => $value]);
    }

    /**
     * Gets an array property.
     *
     * @template T
     *
     * @param string $name    the property name to search for
     * @param T[]    $default the default array if the property is not found or is not valid
     *
     * @return T[]
     */
    protected function getPropertyArray(string $name, array $default): array
    {
        $value = $this->getPropertyString($name);
        if (!\is_string($value)) {
            return $default;
        }

        try {
            $result = StringUtils::decodeJson($value);
            if (\count($result) === \count($default)) {
                return $result;
            }
        } catch (\InvalidArgumentException) {
        }

        return $default;
    }

    /**
     * Gets a boolean property.
     *
     * @param string $name    the property name to search for
     * @param bool   $default the default value if the property is not found
     */
    protected function getPropertyBoolean(string $name, bool $default = false): bool
    {
        return (bool) $this->getCacheValue($name, $default);
    }

    /**
     * Gets a date property.
     *
     * @param string              $name    the property name to search for
     * @param ?\DateTimeInterface $default the default value if the property is not found
     *
     * @psalm-return ($default is null ? (\DateTimeInterface|null) : \DateTimeInterface)
     */
    protected function getPropertyDate(string $name, \DateTimeInterface $default = null): ?\DateTimeInterface
    {
        $timestamp = $this->getPropertyInteger($name);
        if (AbstractProperty::FALSE_VALUE !== $timestamp) {
            $date = \DateTime::createFromFormat('U', (string) $timestamp);
            if ($date instanceof \DateTime) {
                return $date;
            }
        }

        return $default;
    }

    /**
     * Gets an enumeration value.
     *
     * @template T of \BackedEnum
     *
     * @psalm-param T $default
     *
     * @psalm-return T
     */
    protected function getPropertyEnum(string $propertyName, \BackedEnum $default): \BackedEnum
    {
        $defaultValue = $default->value;
        if (\is_int($defaultValue)) {
            $value = $this->getPropertyInteger($propertyName, $defaultValue);
        } else {
            $value = $this->getPropertyString($propertyName, $defaultValue);
        }

        return $default::tryFrom($value) ?? $default;
    }

    /**
     * Gets a float property.
     *
     * @param string $name    the property name to search for
     * @param float  $default the default value if the property is not found
     */
    protected function getPropertyFloat(string $name, float $default = 0.0): float
    {
        return (float) $this->getCacheValue($name, $default);
    }

    /**
     * Gets an integer property.
     *
     * @param string $name    the property name to search for
     * @param int    $default the default value if the property is not found
     */
    protected function getPropertyInteger(string $name, int $default = 0): int
    {
        return (int) $this->getCacheValue($name, $default);
    }

    /**
     * Gets a string property.
     *
     * @param string  $name    the property name to search for
     * @param ?string $default the default value if the property is not found
     *
     * @psalm-return ($default is null ? (string|null) : string)
     */
    protected function getPropertyString(string $name, string $default = null): ?string
    {
        /** @psalm-var mixed $value */
        $value = $this->getCacheValue($name, $default);

        return \is_string($value) ? $value : $default;
    }

    /**
     * Returns if the given value is the default value.
     *
     * @param array<string, mixed> $defaultProperties the default properties to get default value from
     * @param string               $name              the property name
     * @param mixed                $value             the value to compare to
     *
     * @return bool true if default
     */
    protected function isDefaultValue(array $defaultProperties, string $name, mixed $value): bool
    {
        return \array_key_exists($name, $defaultProperties) && $defaultProperties[$name] === $value;
    }

    /**
     * Load the properties.
     *
     * @return array<string, mixed>
     */
    protected function loadProperties(): array
    {
        $this->updateAdapter();

        return [
            // display and edit entities
            self::P_DISPLAY_MODE => $this->getDisplayMode(),
            self::P_EDIT_ACTION => $this->getEditAction(),
            // notification
            self::P_MESSAGE_ICON => $this->isMessageIcon(),
            self::P_MESSAGE_TITLE => $this->isMessageTitle(),
            self::P_MESSAGE_SUB_TITLE => $this->isMessageSubTitle(),
            self::P_MESSAGE_CLOSE => $this->isMessageClose(),
            self::P_MESSAGE_PROGRESS => $this->getMessageProgress(),
            self::P_MESSAGE_POSITION => $this->getMessagePosition(),
            self::P_MESSAGE_TIMEOUT => $this->getMessageTimeout(),
            // home page
            self::P_PANEL_CALCULATION => $this->getPanelCalculation(),
            self::P_PANEL_STATE => $this->isPanelState(),
            self::P_PANEL_MONTH => $this->isPanelMonth(),
            self::P_PANEL_CATALOG => $this->isPanelCatalog(),
            self::P_STATUS_BAR => $this->isStatusBar(),
            self::P_DARK_NAVIGATION => $this->isDarkNavigation(),
            // document options
            self::P_QR_CODE => $this->isQrCode(),
            self::P_PRINT_ADDRESS => $this->isPrintAddress(),
        ];
    }

    /**
     * @param AbstractProperty[]|list<AbstractProperty> $properties
     */
    protected function saveProperties(array $properties): void
    {
        $this->clearCache();
        foreach ($properties as $property) {
            $this->saveDeferredCacheValue($property->getName(), $property->getString());
        }
        $this->saveDeferredCacheValue(self::P_CACHE_SAVED, true);
        if (!$this->commitDeferredValues()) {
            $this->logWarning($this->trans('application_service.commit_error'));
        }
    }

    abstract protected function updateAdapter(): void;
}
