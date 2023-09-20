<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Doctrine\Entity\Behavior;

interface TimestampableInterface
{
    public function getCreatedAt(): ?\DateTimeImmutable;

    public function setCreatedAt(): void;

    public function getUpdatedAt(): ?\DateTimeImmutable;

    public function setUpdatedAt(): void;
}
