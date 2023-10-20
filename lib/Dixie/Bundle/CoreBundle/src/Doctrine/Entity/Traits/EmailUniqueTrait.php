<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Doctrine\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait EmailUniqueTrait
{
    
    #[ORM\Column(type: Types::STRING, length: 120, nullable: true, unique: true)]
    #[Assert\Email(mode: 'strict')]
    #[Assert\Length(max: 120)]
    #[ORM\Column(type: 'string', length: 120, nullable: true, unique: true)]
    #[Assert\Email(mode: 'strict')]
    #[Assert\Length(max: 120)]
    protected ?string $email = null;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email === null ? null : trim($email);

        return $this;
    }
}
