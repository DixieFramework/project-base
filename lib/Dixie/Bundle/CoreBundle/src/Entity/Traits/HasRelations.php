<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Entity\Traits;

use JetBrains\PhpStorm\Pure;
use Doctrine\Common\Collections\Collection;

trait HasRelations
{
    public function sync(string $relation, Collection $items): self
    {
        if (!$this->hasRelation($relation)) return $this;

        $this->removeUnusedRelations($relation, $items);
        $this->addNewRelations($items, $relation);

        return $this;
    }

    private function removeUnusedRelations(string $relation, Collection $items): void
    {
        foreach ($this->{$relation} as $element)
            if (!$items->contains($element))
                $this->{$relation}->removeElement($element);
    }

    private function addNewRelations(Collection $items, string $relation): void
    {
        foreach ($items as $item)
            if (!$this->{$relation}->contains($item))
                $this->{$relation}->add($item);
    }

    private function getForRemoval(string $relation, Collection $items): Collection|array
    {
        /** @var Collection $selfIds */
        $selfIds = $this->{$relation}->map(fn($relation) => $relation->getId());
        if ($selfIds->isEmpty()) {
            return [];
        }
        $alienIds  = $items->map(fn($relation) => $relation->getId());
        $unusedIds = array_diff($selfIds->toArray(), $alienIds->toArray());

        return $this->{$relation}->filter(fn($relation) => in_array($relation->getId(), $unusedIds));
    }

    #[Pure] private function hasRelation(string $relation): bool
    {
        return property_exists($this, $relation);
    }
}
