<?php

declare(strict_types=1);

namespace Talav\PostBundle\Entity;

use Talav\PostBundle\Repository\ViewRepository;
use Doctrine\ORM\Mapping as ORM;
use Talav\UserBundle\Model\UserInterface;

#[ORM\Entity(repositoryClass: ViewRepository::class)]
#[ORM\Table('view')]
class View
{
	#[ORM\Id]
	#[ORM\Column(type: 'integer')]
	#[ORM\GeneratedValue(strategy: 'AUTO')]
    private $id;

    #[ORM\Column(type: 'datetime')]
    private $viewedAt;

    #[ORM\ManyToOne(targetEntity: UserInterface::class, inversedBy: 'views')]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

//    #[ORM\ManyToOne(targetEntity: Song::class, inversedBy: 'views')]
//    private $song;

    #[ORM\Column(type: 'integer')]
    private ?int $quantity;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getViewedAt(): ?\DateTimeInterface
    {
        return $this->viewedAt;
    }

    public function setViewedAt(\DateTimeInterface $viewedAt): self
    {
        $this->viewedAt = $viewedAt;

        return $this;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }

//    public function getSong(): ?Song
//    {
//        return $this->song;
//    }
//
//    public function setSong(?Song $song): self
//    {
//        $this->song = $song;
//
//        return $this;
//    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }
}
