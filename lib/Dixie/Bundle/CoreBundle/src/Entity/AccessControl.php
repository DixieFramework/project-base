<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use App\Repository\AccessControlRepository;

#[ORM\Entity(AccessControlRepository::class)]
class AccessControl
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id;

    #[ORM\Column(length: 64, nullable: false)]
    private ?string $name;

    #[ORM\Column(length: 255, nullable: false)]
    private ?string $path;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $roles;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ips;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $host;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $methods;

    #[ORM\Column(nullable: false)]
    private ?int $allow = 1;

    #[ORM\Column(nullable: false)]
    private int $sort = 0;

    #[ORM\Column()]
    private bool $active = true;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getRoles(): ?string
    {
        return $this->roles;
    }

    public function setRoles(?string $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getIps(): ?string
    {
        return $this->ips;
    }

    public function setIps(?string $ips): self
    {
        $this->ips = $ips;

        return $this;
    }

    public function getHost(): ?string
    {
        return $this->host;
    }

    public function setHost(?string $host): self
    {
        $this->host = $host;

        return $this;
    }

    public function getMethods(): ?string
    {
        return $this->methods;
    }

    public function setMethods(?string $methods): self
    {
        $this->methods = $methods;

        return $this;
    }

    public function getAllow(): ?int
    {
        return $this->allow;
    }

    public function setAllow(int $allow): self
    {
        $this->allow = $allow;

        return $this;
    }

    public function getSort(): ?int
    {
        return $this->sort;
    }

    public function setSort(int $sort): self
    {
        $this->sort = $sort;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

}
