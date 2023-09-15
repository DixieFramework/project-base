<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Form\DataTransformer;

use Talav\CoreBundle\Entity\AbstractEntity;
use Talav\CoreBundle\Repository\AbstractRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Abstract data transformer to convert an entity to an identifier (integer).
 *
 * @template T of AbstractEntity
 *
 * @template-implements DataTransformerInterface<T, int>
 */
class AbstractEntityTransformer implements DataTransformerInterface
{
    /**
     * @var class-string<T>
     */
    private readonly string $className;

    /**
     * Constructor.
     *
     * @param AbstractRepository<T> $repository
     */
    public function __construct(private readonly AbstractRepository $repository)
    {
        $this->className = $this->repository->getClassName();
    }

    /**
     * @param int|string|null $value
     *
     * @return T|null
     */
    public function reverseTransform(mixed $value): ?AbstractEntity
    {
        if (null === $value || '' === $value) {
            return null;
        }

        if (!\is_numeric($value)) {
            $message = \sprintf('A "number" expected, a "%s" given.', \get_debug_type($value));
            throw new TransformationFailedException($message);
        }

        $entity = $this->repository->find((int) $value);
        if (!\is_a($entity, $this->className)) {
            $message = \sprintf('Unable to find a "%s" for the value "%s".', $this->className, $value);
            throw new TransformationFailedException($message);
        }

        return $entity;
    }

    /**
     * @param AbstractEntity|null $value
     */
    public function transform(mixed $value): ?int
    {
        if (null === $value) {
            return null;
        }

        if (!\is_a($value, $this->className)) {
            $message = \sprintf('A "%s" expected, a "%s" given.', $this->className, \get_debug_type($value));
            throw new TransformationFailedException($message);
        }

        return $value->getId();
    }
}
