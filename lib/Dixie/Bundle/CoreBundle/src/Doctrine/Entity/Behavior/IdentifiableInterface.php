<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Doctrine\Entity\Behavior;

interface IdentifiableInterface
{
    public function getId(): ?int;

    public function getEntityName(): string;
}
