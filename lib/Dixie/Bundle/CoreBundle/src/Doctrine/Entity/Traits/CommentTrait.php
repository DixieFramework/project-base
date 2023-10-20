<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Doctrine\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait CommentTrait
{
    
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(max: 65000)]
    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\Length(max: 65000)]
    protected ?string $comment = null;

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment === null ? null :  trim($comment);

        return $this;
    }
}
