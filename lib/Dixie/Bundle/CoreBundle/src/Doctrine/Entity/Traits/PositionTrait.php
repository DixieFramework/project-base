<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Doctrine\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait PositionTrait
{
    
    #[ORM\Column(type: Types::SMALLINT, nullable: false, options: ['default' => 0, 'unsigned' => true])]
    #[Assert\Range(min: 0, max: 65000)]
    #[ORM\Column(type: 'smallint', nullable: false, options: ['default' => 0, 'unsigned' => true])]
    #[Assert\Range(min: '0', max: '65000')]
    protected int $position = 0;

    public function setPosition(?int $position): self
    {
        $this->position = ($position === null) ? 0 : $position;

        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }
}
