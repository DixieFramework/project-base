<?php

declare(strict_types=1);

namespace Talav\PermissionBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Talav\Component\Resource\Model\ResourceInterface;

interface PermissionInterface extends ResourceInterface
{
    public function getName(): string;

    public function setName(string $name): self;

    public function getRoles(): Collection;
}
