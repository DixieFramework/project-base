<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Talav\ProfileBundle\Repository\UserAttributeRepository;

#[ORM\Entity(repositoryClass: UserAttributeRepository::class)]
#[ORM\Table('user_attribute')]
class UserAttribute
{
//    /**
//     * @Id()
//     * @OneToOne(targetEntity="Attribute")
//     * @JoinColumn(name = "attribute_id", referencedColumnName = "id")
//     */
    protected Attribute $attribute;

//    /**
//     * @Id()
//     * @OneToOne(targetEntity="user")
//     * @JoinColumn(name = "user_id", referencedColumnName = "id")
//     */
    protected ProfileInterface $profile;

    public function getProfile(): ProfileInterface
    {
        return $this->profile;
    }

    public function setProfile(ProfileInterface $profile): void
    {
        $this->profile = $profile;
    }

    public function getAttribute(): Attribute
    {
        return $this->attribute;
    }

    public function setAttribute(Attribute $attribute): void
    {
        $this->attribute = $attribute;
    }
}
