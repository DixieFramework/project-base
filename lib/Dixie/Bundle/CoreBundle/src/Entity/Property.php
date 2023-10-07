<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Entity;

use App\Repository\PropertyRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Represents an application property.
 */
#[ORM\Table(name: 'talav_property')]
#[ORM\Entity(repositoryClass: PropertyRepository::class)]
#[ORM\UniqueConstraint(name: 'unique_property_name', columns: ['name'])]
#[UniqueEntity(fields: 'name', message: 'property.unique_name')]
class Property extends AbstractProperty
{
    /**
     * Create a new instance for the given.
     */
    public static function instance(string $name): self
    {
        return new self($name);
    }
}
