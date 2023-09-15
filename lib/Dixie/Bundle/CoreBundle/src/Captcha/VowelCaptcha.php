<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Captcha;

/**
 * Alpha captcha to validate a vowel.
 */
class VowelCaptcha extends AbstractAlphaCaptcha
{
    private const INDEX_MAPPING = [
        '0' => 'first',
        '1' => 'second',
        '2' => 'third',
        '-1' => 'last',
    ];

    private const VOWEL = 'AEIOUY';

    /**
     * Gets the default index name.
     */
    public static function getDefaultIndexName(): string
    {
        return 'VowelCaptcha';
    }

    protected function getAnswer(string $word, int $letterIndex): string
    {
        return $this->findAnswer($word, $letterIndex, self::VOWEL);
    }

    protected function getLetterIndex(): int
    {
        return \array_rand(self::INDEX_MAPPING);
    }

    protected function getQuestion(string $word, int $letterIndex): string
    {
        $params = [
            '%index%' => $this->trans(self::INDEX_MAPPING[$letterIndex], [], 'captcha'),
            '%letter%' => $this->trans('vowel', [], 'captcha'),
            '%word%' => $word,
        ];

        return $this->trans('sentence', $params, 'captcha');
    }
}
