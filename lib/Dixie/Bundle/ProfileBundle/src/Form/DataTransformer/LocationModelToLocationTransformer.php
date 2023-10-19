<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Form\DataTransformer;

use Talav\WebBundle\Entity\Location;
use Talav\WebBundle\Entity\NewLocation;
use Talav\ProfileBundle\Form\Model\LocationModel;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class LocationModelToLocationTransformer implements DataTransformerInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Transforms a location to a location request.
     *
     * @param mixed $value
     */
    public function transform($value): ?LocationModel
    {
        if (null === $value) {
            return null;
        }

        $locationRequest = new LocationModel($value);

        return $locationRequest;
    }

    /**
     * Transforms a location request to a location.
     *
     * @param LocationModel $value
     *
     * @throws TransformationFailedException if location is not found
     */
    public function reverseTransform($value): ?NewLocation
    {
        if (null === $value) {
            return null;
        }

        if (null === $value->geonameId) {
            $failure = new TransformationFailedException('trip.error.location.none.given');
            $failure->setInvalidMessage('trip.error.location.none.given');
            throw $failure;
        }

        /** @var Location $location */
        $location = $this->entityManager
            ->getRepository(NewLocation::class)
            ->findOneBy(['geonameId' => $value->geonameId]);

        if (null === $location) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            $message = sprintf(
                'A location with geonameId "%d" for %s does not exist!',
                $value->geonameId,
                $value->name
            );
            throw new TransformationFailedException($message);
        }

        return $location;
    }
}
