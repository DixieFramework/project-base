<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Service;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Talav\CoreBundle\Traits\PropertyServiceTrait;
use Talav\ProfileBundle\Entity\UserProperty;
use Talav\ProfileBundle\Repository\UserPropertyRepository;

//INSERT INTO `region` (`id`, `country_id`, `name`) VALUES (1, 1, 'Nordjyland');
//INSERT INTO `region` (`id`, `country_id`, `name`) VALUES (2, 1, 'Midtjylland');
//INSERT INTO `region` (`id`, `country_id`, `name`) VALUES (3, 1, 'Syddanmark');
//INSERT INTO `region` (`id`, `country_id`, `name`) VALUES (4, 1, 'Hovedstaden');
//INSERT INTO `region` (`id`, `country_id`, `name`) VALUES (5, 1, 'Sjælland');


//INSERT INTO `region` (`id`, `country_id`, `name`) VALUES (4, 1, 'Faroe Islands');

/**
 * Service to manage user properties.
 */
class ProfileService implements PropertyServiceInterface, ServiceSubscriberInterface
{
    use PropertyServiceTrait;

    public function __construct(
        private readonly UserPropertyRepository $userPropertyRepository,
        private readonly Security               $security,
        #[Target('user_cache')]
        CacheItemPoolInterface                  $cacheItemPool
    )
    {
        $this->setCacheItemPool($cacheItemPool);
    }

//    /**
//     * Gets the application service.
//     */
//    public function getApplication(): ApplicationService
//    {
//        return $this->service;
//    }
//
//    public function getCustomer(): CustomerInformation
//    {
//        $customer = $this->service->getCustomer();
//        $customer->setPrintAddress($this->isPrintAddress());
//
//        return $customer;
//    }
//
//    public function getDisplayMode(): TableView
//    {
//        return $this->getPropertyEnum(self::P_DISPLAY_MODE, $this->service->getDisplayMode());
//    }
//
//    public function getEditAction(): EntityAction
//    {
//        return $this->getPropertyEnum(self::P_EDIT_ACTION, $this->service->getEditAction());
//    }
//
//    /**
//     * Gets the message attributes.
//     */
//    public function getMessageAttributes(): array
//    {
//        return [
//            'icon' => $this->isMessageIcon(),
//            'title' => $this->isMessageTitle(),
//            'display-close' => $this->isMessageClose(),
//            'display-subtitle' => $this->isMessageSubTitle(),
//            'timeout' => $this->getMessageTimeout(),
//            'progress' => $this->getMessageProgress(),
//            'position' => $this->getMessagePosition()->value,
//        ];
//    }
//
//    public function getMessagePosition(): MessagePosition
//    {
//        return $this->getPropertyEnum(self::P_MESSAGE_POSITION, $this->service->getMessagePosition());
//    }
//
//    public function getMessageProgress(): int
//    {
//        return $this->getPropertyInteger(self::P_MESSAGE_PROGRESS, $this->service->getMessageProgress());
//    }
//
//    public function getMessageTimeout(): int
//    {
//        return $this->getPropertyInteger(self::P_MESSAGE_TIMEOUT, $this->service->getMessageTimeout());
//    }
//
//    public function getPanelCalculation(): int
//    {
//        return $this->getPropertyInteger(self::P_PANEL_CALCULATION, $this->service->getPanelCalculation());
//    }
//
//    /**
//     * Gets all properties.
//     *
//     * @return array<string, mixed>
//     */
//    public function getProperties(): array
//    {
//        return $this->loadProperties();
//    }
//
//    public function isDarkNavigation(): bool
//    {
//        return $this->getPropertyBoolean(self::P_DARK_NAVIGATION, $this->service->isDarkNavigation());
//    }
//
//    public function isMessageClose(): bool
//    {
//        return $this->getPropertyBoolean(self::P_MESSAGE_CLOSE, $this->service->isMessageClose());
//    }
//
//    public function isMessageIcon(): bool
//    {
//        return $this->getPropertyBoolean(self::P_MESSAGE_ICON, $this->service->isMessageIcon());
//    }
//
//    public function isMessageSubTitle(): bool
//    {
//        return $this->getPropertyBoolean(self::P_MESSAGE_SUB_TITLE, $this->service->isMessageSubTitle());
//    }
//
//    public function isMessageTitle(): bool
//    {
//        return $this->getPropertyBoolean(self::P_MESSAGE_TITLE, $this->service->isMessageTitle());
//    }
//
//    public function isPanelCatalog(): bool
//    {
//        return $this->getPropertyBoolean(self::P_PANEL_CATALOG, $this->service->isPanelCatalog());
//    }
//
//    public function isPanelMonth(): bool
//    {
//        return $this->getPropertyBoolean(self::P_PANEL_MONTH, $this->service->isPanelMonth());
//    }
//
//    public function isPanelState(): bool
//    {
//        return $this->getPropertyBoolean(self::P_PANEL_STATE, $this->service->isPanelState());
//    }
//
//    public function isPrintAddress(): bool
//    {
//        return $this->getPropertyBoolean(self::P_PRINT_ADDRESS, $this->service->isPrintAddress());
//    }
//
//    public function isQrCode(): bool
//    {
//        return $this->getPropertyBoolean(self::P_QR_CODE, $this->service->isQrCode());
//    }
//
//    public function isStatusBar(): bool
//    {
//        return $this->getPropertyBoolean(self::P_STATUS_BAR, $this->service->isStatusBar());
//    }

    /**
     * @param array<string, mixed> $properties
     */
    public function setProperties(array $properties): static
    {
        if ([] === $properties) {
            return $this;
        }
        $user = $this->getUser();
        if (!$user instanceof UserInterface) {
            return $this;
        }

        $defaultValues = $this->service->getProperties();
        /** @psalm-var mixed $value */
        foreach ($properties as $key => $value) {
            $this->saveProperty($key, $value, $defaultValues, $user);
        }
        $this->userPropertyRepository->flush();
        $this->updateAdapter();

        return $this;
    }

    protected function updateAdapter(): void
    {
        $user = $this->getUser();
        if (!$user instanceof UserInterface) {
            return;
        }
        $properties = $this->userPropertyRepository->findByUser($user);
        $this->saveProperties($properties);
    }

    private function getUser(): ?UserInterface
    {
        return $this->security->getUser();
    }

    /**
     * Update a property without saving changes to database.
     *
     * @param array<string, mixed> $defaultValues
     */
    private function saveProperty(string $name, mixed $value, array $defaultValues, UserInterface $user): void
    {
        $property = $this->userPropertyRepository->findOneByUserAndName($user, $name);
        if ($this->isDefaultValue($defaultValues, $name, $value)) {
            if ($property instanceof UserProperty) {
                $this->userPropertyRepository->remove($property, false);
            }
        } else {
            if (!$property instanceof UserProperty) {
                $property = UserProperty::instance($name, $user);
                $this->userPropertyRepository->add($property, false);
            }
            $property->setValue($value);
        }
    }
}
