<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Doctrine\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait TypeIntTrait
{
    /**
     * @ORM\Column(type="smallint", nullable=false, options={"default":0})
     */
    #[ORM\Column(type: Types::SMALLINT, nullable: false, options: ['default' => 0])]
    protected int $type;

    public function getType(): int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }
}
