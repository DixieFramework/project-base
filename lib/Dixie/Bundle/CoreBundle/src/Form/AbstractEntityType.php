<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Form;

use Talav\CoreBundle\Entity\AbstractEntity;
use Talav\CoreBundle\Traits\CheckSubClassTrait;
use Talav\CoreBundle\Utils\StringUtils;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Edition type to use within an {@link AbstractEntity} class.
 *
 * @template T of AbstractEntity
 */
abstract class AbstractEntityType extends AbstractHelperType
{
    use CheckSubClassTrait;

    /**
     * The entity class name.
     *
     * @psalm-var class-string<T> $className
     */
    protected string $className;

    /**
     * Constructor.
     *
     * @param class-string<T> $className the entity class name
     *
     * @throws \InvalidArgumentException if the given class name is not a subclass of the AbstractEntity class
     */
    protected function __construct(string $className)
    {
//        $this->checkSubClass($className, AbstractEntity::class);
        $this->className = $className;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', $this->className);
    }

    protected function getLabelPrefix(): ?string
    {
        $name = \strtolower(StringUtils::getShortName($this->className));

        return "$name.fields.";
    }
}
