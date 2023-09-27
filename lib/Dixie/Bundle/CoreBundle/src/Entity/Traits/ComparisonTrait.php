<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Entity\Traits;

use Talav\CoreBundle\Entity\Interfaces\EntityInterface;

trait ComparisonTrait
{
    public function isCompare(EntityInterface $entity): bool
    {
        return $this->getId() === $entity->getId();
    }
}