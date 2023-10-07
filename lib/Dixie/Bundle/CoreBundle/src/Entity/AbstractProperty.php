<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Entity;

use Talav\Component\Resource\Model\ResourceInterface;
use Talav\Component\Resource\Model\ResourceTrait;
use Talav\CoreBundle\Utils\StringUtils;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Represent an abstract property.
 */
#[ORM\MappedSuperclass]
abstract class AbstractProperty extends AbstractEntity implements ResourceInterface
{
    /**
     * The value used for FALSE or 0 value.
     */
    final public const FALSE_VALUE = 0;

    /**
     * The value used for TRUE value.
     */
    final public const TRUE_VALUE = 1;

    /**
     * The property name.
     */
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    #[ORM\Column(length: 50)]
    protected ?string $name;

    /**
     * The property value.
     */
    #[Assert\NotBlank]
    #[Assert\Length(max: self::MAX_STRING_LENGTH)]
    #[ORM\Column(nullable: true)]
    protected ?string $value = null;

    /**
     * Constructor.
     *
     * @param ?string $name the optional name
     */
    public function __construct(string $name = null)
    {
        $this->name = $name;
    }

    /**
     * Gets this property value as an array. Internally the array is decoded from a JSON string.
     */
    public function getArray(): ?array
    {
        if (!empty($this->value)) {
            try {
                return StringUtils::decodeJson($this->value);
            } catch (\InvalidArgumentException) {
            }
        }

        return null;
    }

    /**
     * Gets this property value as boolean.
     */
    public function getBoolean(): bool
    {
        return self::FALSE_VALUE !== $this->getInteger();
    }

    /**
     * Gets this property value as date.
     */
    public function getDate(): ?\DateTimeInterface
    {
        $timestamp = $this->getInteger();
        if (self::FALSE_VALUE !== $timestamp) {
            $date = \DateTime::createFromFormat('U', (string) $timestamp);
            if ($date instanceof \DateTime) {
                return $date;
            }
        }

        return null;
    }

    public function getDisplay(): string
    {
        return $this->name ?? parent::getDisplay();
    }

    /**
     * Gets this property value as integer.
     */
    public function getInteger(): int
    {
        return (int) ($this->value ?? self::FALSE_VALUE);
    }

    /**
     * Gets the property name.
     */
    public function getName(): string
    {
        return (string) $this->name;
    }

    /**
     * Gets the property value as string.
     */
    public function getString(): ?string
    {
        return $this->value;
    }

    /**
     * Sets the property value as an array. Internally the array is encoded as JSON string.
     */
    public function setArray(?array $value): static
    {
        return $this->setString(null === $value || [] === $value ? null : StringUtils::encodeJson($value));
    }

    /**
     * Sets the property value as boolean.
     */
    public function setBoolean(bool $value): static
    {
        return $this->setInteger($value ? self::TRUE_VALUE : self::FALSE_VALUE);
    }

    /**
     * Sets the property value as date.
     */
    public function setDate(?\DateTimeInterface $value): static
    {
        return $this->setInteger($value instanceof \DateTimeInterface ? $value->getTimestamp() : self::FALSE_VALUE);
    }

    /**
     * Sets the property value as integer.
     */
    public function setInteger(int $value): static
    {
        return $this->setString((string) $value);
    }

    /**
     * Sets the property name.
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Sets the property value as string.
     */
    public function setString(?string $value): static
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Sets the property value.
     *
     * This function try first to convert the value to an appropriate type (bool, int, etc...).
     *
     * @param mixed $value the value to set
     */
    public function setValue(mixed $value): static
    {
        if (\is_bool($value)) {
            return $this->setBoolean($value);
        }
        if (\is_int($value)) {
            return $this->setInteger($value);
        }
        if (\is_array($value)) {
            return $this->setArray($value);
        }
        if ($value instanceof \DateTimeInterface) {
            return $this->setDate($value);
        }
        if ($value instanceof AbstractEntity) {
            return $this->setInteger((int) $value->getId());
        }
        if ($value instanceof \BackedEnum) {
            return $this->setString((string) $value->value);
        }

        return $this->setString((string) $value);
    }
}
