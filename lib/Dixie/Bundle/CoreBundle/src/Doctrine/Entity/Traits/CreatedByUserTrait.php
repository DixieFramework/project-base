<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Doctrine\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

trait CreatedByUserTrait
{
    #[ORM\ManyToOne(targetEntity: UserInterface::class)]
    #[ORM\ManyToOne(targetEntity: 'Symfony\Component\Security\Core\User\UserInterface')]
    protected ?UserInterface $created_by_user = null;

    /**
     * @return \Talav\CoreBundle\Entity\User|UserInterface|null
     */
    public function getCreatedByUser(): ?UserInterface
    {
        return $this->created_by_user;
    }

    public function setCreatedByUser(?UserInterface $created_by_user = null): self
    {
        $this->created_by_user = $created_by_user;

        return $this;
    }
}
