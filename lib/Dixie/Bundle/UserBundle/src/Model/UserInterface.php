<?php

declare(strict_types=1);

namespace Talav\UserBundle\Model;

use Talav\Component\User\Model\UserInterface as BaseUser;
use Talav\ProfileBundle\Entity\ProfileInterface;
use Talav\ProfileBundle\Entity\UserMetadata;

interface UserInterface extends BaseUser
{
    /**
     * @return ProfileInterface|null
     */
    public function getProfile(): ?ProfileInterface;

    /**
     * @param ProfileInterface|null $profile
     */
    public function setProfile(?ProfileInterface $profile): void;

    public function getMetadata(string $key): ?UserMetadata;

    public function getMetadataValue(string $key): string;

    public function setMetadata(string $key, string $value): self;


    /**
     * Returns UserFlagKey::PROFILE_COMPLETED value.
     *
     * @return bool
     */
    public function isProfileCompleted(): bool;

    /**
     * Sets UserFlagKey::PROFILE_COMPLETED value.
     *
     * @param $profileCompleted
     * @return $this|UserInterface
     */
    public function setProfileCompleted($profileCompleted): UserInterface;

    /**
     * Sets whether to send the creation email or not.
     * (non mapped)
     *
     * @return $this|UserInterface
     */
    public function setSendCreationEmail(bool $send): UserInterface;

    /**
     * Returns whether to send the creation email or not.
     * (non mapped)
     */
    public function getSendCreationEmail(): bool;
}
