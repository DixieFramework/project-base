<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Doctrine\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait TitleUniqueNotBlankTrait
{
    
    #[ORM\Column(type: Types::STRING, length: 255, nullable: false, unique: true)]
    #[Assert\Length(max: 255)]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    #[ORM\Column(type: 'string', length: 255, nullable: false, unique: true)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(max: 255)]
    protected string $title;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = trim((string) $title);

        return $this;
    }
}
