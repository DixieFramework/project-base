<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Doctrine\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

trait UserTrait
{
    #[ORM\ManyToOne(targetEntity: UserInterface::class)]
    #[ORM\JoinColumn(nullable: true)]
    #[ORM\ManyToOne(targetEntity: 'Symfony\Component\Security\Core\User\UserInterface')]
    #[ORM\JoinColumn(nullable: true)]
    protected ?UserInterface $user = null;

    /**
     * @return \Talav\CoreBundle\Entity\User|UserInterface|null
     */
    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }
}
