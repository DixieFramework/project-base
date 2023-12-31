<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Form\Type;

use Talav\CoreBundle\Entity\AbstractEntity;
use Talav\CoreBundle\Traits\TranslatorAwareTrait;
use Talav\CoreBundle\Utils\FormatUtils;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Symfony\Contracts\Service\ServiceSubscriberTrait;

/**
 * A form type that just renders the field as a span tag.
 *
 * This is useful for forms where certain field need to be shown but not editable.
 * If the 'expanded' option is set to true, a div tag is added around the span tag.
 *
 * @extends AbstractType<mixed>
 */
class PlainType extends AbstractType implements ServiceSubscriberInterface
{
    use ServiceSubscriberTrait;
    use TranslatorAwareTrait;

    /**
     * The gregorian calendar type.
     */
    final public const CALENDAR_GREGORIAN = \IntlDateFormatter::GREGORIAN;

    /**
     * The traditional calendar type.
     */
    final public const CALENDAR_TRADITIONAL = \IntlDateFormatter::TRADITIONAL;

    /**
     * The full date or time format.
     */
    final public const FORMAT_FULL = \IntlDateFormatter::FULL;

    /**
     * The long date or time format.
     */
    final public const FORMAT_LONG = \IntlDateFormatter::LONG;

    /**
     * The medium date or time format.
     */
    final public const FORMAT_MEDIUM = \IntlDateFormatter::MEDIUM;

    /**
     * The none date or time format.
     */
    final public const FORMAT_NONE = \IntlDateFormatter::NONE;

    /**
     * The short date or time format.
     */
    final public const FORMAT_SHORT = \IntlDateFormatter::SHORT;

    /**
     * The amount number pattern.
     */
    final public const NUMBER_AMOUNT = 'price';

    /**
     * The identifier number pattern.
     */
    final public const NUMBER_IDENTIFIER = 'identifier';

    /**
     * The integer number pattern.
     */
    final public const NUMBER_INTEGER = 'integer';

    /**
     * The percent number pattern.
     */
    final public const NUMBER_PERCENT = 'percent';

    /**
     * @psalm-param FormView<\Symfony\Component\Form\FormTypeInterface> $view
     * @psalm-param FormInterface<\Symfony\Component\Form\FormTypeInterface> $form
     *
     * @psalm-suppress InvalidPropertyAssignmentValue
     *
     * @phpstan-param FormView<\Symfony\Component\Form\FormTypeInterface<mixed>> $view
     * @phpstan-param FormInterface<\Symfony\Component\Form\FormTypeInterface<mixed>> $form
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        parent::buildView($view, $form, $options);

        /** @psalm-var mixed $data */
        $data = $form->getViewData();
        $value = $this->getDataValue($data, $options);
        $display_value = $this->getDisplayValue($data, $options) ?? $value;

        $view->vars['value'] = $value;
        $view->vars['display_value'] = $display_value;
        $view->vars['expanded'] = $options['expanded'];
        $view->vars['hidden_input'] = $options['hidden_input'];
        $view->vars['text_class'] = $options['text_class'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $this->configureDefaults($resolver);
        $this->configureNumber($resolver);
        $this->configureDate($resolver);
    }

    private function configureDate(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'date_format' => null,
            'time_format' => null,
            'date_pattern' => null,
            'time_zone' => null,
            'calendar' => null,
        ]);

        $resolver->setAllowedTypes('date_format', [
            'null',
            'int',
        ])->setAllowedValues('date_format', [
            null,
            self::FORMAT_FULL,
            self::FORMAT_LONG,
            self::FORMAT_MEDIUM,
            self::FORMAT_SHORT,
            self::FORMAT_NONE,
        ]);

        $resolver->setAllowedTypes('time_format', [
            'null',
            'int',
        ])->setAllowedValues('time_format', [
            null,
            self::FORMAT_FULL,
            self::FORMAT_LONG,
            self::FORMAT_MEDIUM,
            self::FORMAT_SHORT,
            self::FORMAT_NONE,
        ]);

        $resolver->setAllowedTypes('date_pattern', [
            'null',
            'string',
        ]);

        $resolver->setAllowedTypes('time_zone', [
            'null',
            'string',
        ]);

        $resolver->setAllowedTypes('calendar', [
            'null',
            'int',
        ])->setAllowedValues('calendar', [
            null,
            self::CALENDAR_GREGORIAN,
            self::CALENDAR_TRADITIONAL,
        ]);
    }

    private function configureDefaults(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'hidden_input' => false,
            'read_only' => true,
            'disabled' => true,
            'required' => false,
            'expanded' => false,
            'empty_value' => null,
            'compound' => false,
            'separator' => ', ',
            'value_transformer' => null,
            'display_transformer' => null,
            'text_class' => null,
        ]);

        $resolver->setAllowedTypes('hidden_input', 'bool')
            ->setAllowedTypes('read_only', 'bool')
            ->setAllowedTypes('disabled', 'bool')
            ->setAllowedTypes('required', 'bool')
            ->setAllowedTypes('expanded', 'bool');

        $resolver->setAllowedTypes('empty_value', [
            'null',
            'string',
            'callable',
        ]);

        $resolver->setAllowedTypes('separator', [
            'null',
            'string',
        ]);

        $resolver->setAllowedTypes('value_transformer', [
            'null',
            'callable',
        ]);

        $resolver->setAllowedTypes('display_transformer', [
            'null',
            'callable',
        ]);

        $resolver->setAllowedTypes('text_class', [
            'null',
            'string',
        ]);
    }

    private function configureNumber(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'number_pattern' => null,
            'percent_sign' => true,
            'percent_decimals' => 2,
            'percent_rounding_mode' => \NumberFormatter::ROUND_HALFEVEN,
        ]);

        $resolver->setAllowedTypes('number_pattern', [
            'null',
            'string',
        ])->setAllowedValues('number_pattern', [
            null,
            self::NUMBER_IDENTIFIER,
            self::NUMBER_INTEGER,
            self::NUMBER_PERCENT,
            self::NUMBER_AMOUNT,
        ]);

        $resolver->setAllowedTypes('percent_sign', 'bool')
            ->setAllowedTypes('percent_decimals', 'int')
            ->setAllowedTypes('percent_rounding_mode', 'int')
            ->setAllowedValues('percent_rounding_mode', [
                \NumberFormatter::ROUND_CEILING,
                \NumberFormatter::ROUND_FLOOR,
                \NumberFormatter::ROUND_DOWN,
                \NumberFormatter::ROUND_UP,
                \NumberFormatter::ROUND_HALFEVEN,
                \NumberFormatter::ROUND_HALFDOWN,
                \NumberFormatter::ROUND_HALFUP,
            ]);
    }

    private function formatPercent(float|int|string $value, array $options): string
    {
        $includeSign = $this->isOptionBool($options, 'percent_sign', true);
        $decimals = $this->getOptionInt($options, 'percent_decimals', 2);
        $roundingMode = $this->getOptionInt($options, 'percent_rounding_mode', \NumberFormatter::ROUND_HALFEVEN);

        return FormatUtils::formatPercent((float) $value, $includeSign, $decimals, $roundingMode);
    }

    /**
     * @throws TransformationFailedException if the value can not be mapped to a string
     */
    private function getDataValue(mixed $value, array $options): string
    {
        // transform callback
        /** @psalm-var mixed $value */
        $value = $this->transformValue($value, $options);

        // boolean?
        if (\is_bool($value)) {
            return $this->transformBool($value);
        }

        // empty?
        if (null === $value || '' === $value) {
            return $this->transformEmpty($value, $options);
        }

        // array?
        if (\is_array($value)) {
            return $this->transformArray($value, $options);
        }

        // entity?
        if ($value instanceof AbstractEntity) {
            return $value->getDisplay();
        }

        // date?
        if ($value instanceof \DateTimeInterface) {
            return $this->transformDate($value, $options);
        }

        // numeric?
        if (\is_numeric($value)) {
            return $this->transformNumber($value, $options);
        }

        // to string?
        if (\is_scalar($value) || (\is_object($value) && \method_exists($value, '__toString'))) {
            return (string) $value;
        }

        // error
        throw new TransformationFailedException(\sprintf('Unable to map the instance of "%s" to a string.', \get_debug_type($value)));
    }

    private function getDisplayValue(mixed $value, array $options): ?string
    {
        if (\is_callable($options['display_transformer'])) {
            return (string) \call_user_func($options['display_transformer'], $value);
        }

        return null;
    }

    private function getOptionInt(array $options, string $name, int $defaultValue): int
    {
        if (isset($options[$name]) && \is_int($options[$name])) {
            return $options[$name];
        }

        return $defaultValue;
    }

    /**
     * @psalm-return ($defaultValue is null ? (string|null) : string)
     */
    private function getOptionString(array $options, string $name, string $defaultValue = null, bool $translate = false): ?string
    {
        $value = isset($options[$name]) && \is_string($options[$name]) ? $options[$name] : $defaultValue;

        return $translate ? $this->trans((string) $value) : $value;
    }

    private function isOptionBool(array $options, string $name, bool $defaultValue): bool
    {
        if (isset($options[$name]) && \is_bool($options[$name])) {
            return $options[$name];
        }

        return $defaultValue;
    }

    private function transformArray(array $value, array $options): string
    {
        $callback = fn (mixed $item): string => $this->getDataValue($item, $options);
        $values = \array_map($callback, $value);
        $separator = $this->getOptionString($options, 'separator', ', ');

        return \implode($separator, $values);
    }

    private function transformBool(bool $value): string
    {
        return $value ? $this->trans('common.value_true') : $this->trans('common.value_false');
    }

    private function transformDate(\DateTimeInterface|int|null $value, array $options): string
    {
        $timezone = $this->getOptionString($options, 'time_zone');
        $pattern = $this->getOptionString($options, 'date_pattern');
        $calendar = $this->getOptionInt($options, 'calendar', self::CALENDAR_GREGORIAN);
        $date_type = $this->getOptionInt($options, 'date_format', FormatUtils::getDateType());
        $time_type = $this->getOptionInt($options, 'time_format', FormatUtils::getTimeType());

        return (string) FormatUtils::formatDateTime($value, $date_type, $time_type, $timezone, $calendar, $pattern);
    }

    private function transformEmpty(mixed $value, array $options): string
    {
        if (\is_callable($options['empty_value'])) {
            return (string) \call_user_func($options['empty_value'], $value);
        }

        return $this->getOptionString($options, 'empty_value', 'common.value_null', true);
    }

    /**
     * Formats the given value as number.
     *
     * @param float|int|string $value   the value to transform
     * @param array            $options the options
     *
     * @return string the formatted number
     */
    private function transformNumber(float|int|string $value, array $options): string
    {
        $type = $this->getOptionString($options, 'number_pattern', '');

        return match ($type) {
            self::NUMBER_IDENTIFIER => FormatUtils::formatId((int) $value),
            self::NUMBER_INTEGER => FormatUtils::formatInt((int) $value),
            self::NUMBER_AMOUNT => FormatUtils::formatAmount((float) $value),
            self::NUMBER_PERCENT => $this->formatPercent($value, $options),
            default => (string) $value
        };
    }

    private function transformValue(mixed $value, array $options): mixed
    {
        if (\is_callable($options['value_transformer'])) {
            return \call_user_func($options['value_transformer'], $value);
        }

        return $value;
    }
}
