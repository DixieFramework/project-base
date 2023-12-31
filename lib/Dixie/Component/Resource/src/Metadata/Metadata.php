<?php

declare(strict_types=1);

namespace Talav\Component\Resource\Metadata;

use Doctrine\Common\Inflector\Inflector;

final class Metadata implements MetadataInterface
{
    private string $name;

    private string $applicationName;

    private iterable $parameters;

    private function __construct(string $name, string $applicationName, array $parameters)
    {
        $this->name = $name;
        $this->applicationName = $applicationName;
        $this->parameters = $parameters;
    }

    public static function fromAliasAndConfiguration(string $alias, array $parameters): self
    {
        [$applicationName, $name] = self::parseAlias($alias);

        return new self($name, $applicationName, $parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias(): string
    {
        return $this->applicationName.'.'.$this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getApplicationName(): string
    {
        return $this->applicationName;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getHumanizedName(): string
    {
        return strtolower(trim((string) preg_replace(['/([A-Z])/', '/[_\s]+/'], ['_$1', ' '], $this->name)));
    }

    /**
     * {@inheritdoc}
     */
    public function getPluralName(): string
    {
        return Inflector::pluralize($this->name);
    }

    /**
     * {@inheritdoc}
     */
    public function getParameter(string $name)
    {
        if (!$this->hasParameter($name)) {
            throw new \InvalidArgumentException(sprintf('Parameter "%s" is not configured for resource "%s".', $name, $this->getAlias()));
        }

        return $this->parameters[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function hasParameter(string $name): bool
    {
        return array_key_exists($name, $this->parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function getClass(string $name): string
    {
        if (!$this->hasClass($name)) {
            throw new \InvalidArgumentException(sprintf('Class "%s" is not configured for resource "%s".', $name, $this->getAlias()));
        }

        return $this->parameters['classes'][$name];
    }

    /**
     * {@inheritdoc}
     */
    public function hasClass(string $name): bool
    {
        return isset($this->parameters['classes'][$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getServiceId(string $serviceName): string
    {
        return sprintf('%s.%s.%s', $this->applicationName, $serviceName, $this->name);
    }

    /**
     * {@inheritdoc}
     */
    public function getServiceClass(string $serviceName): string
    {
        return sprintf('%s.%s.%s.class', $this->applicationName, $serviceName, $this->name);
    }

    private static function parseAlias(string $alias): array
    {
        if (false === strpos($alias, '.')) {
            throw new \InvalidArgumentException(sprintf('Invalid alias "%s" supplied, it should conform to the following format "<applicationName>.<name>".', $alias));
        }

        return explode('.', $alias);
    }
}
