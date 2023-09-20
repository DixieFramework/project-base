<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Doctrine\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait Slug255Trait
{
    /**
     * @ORM\Column(type="string", length=255, nullable=false, unique=true)
     *
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Length(max=255)
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: false, unique: true)]
    #[Assert\Length(max: 255)]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    protected string $slug;

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = trim((string) $slug);

        $encoding = mb_detect_encoding($this->slug);
        $this->slug = $encoding
            ? mb_convert_case($this->slug, MB_CASE_LOWER, $encoding)
            : mb_convert_case($this->slug, MB_CASE_LOWER);

        return $this;
    }
}
