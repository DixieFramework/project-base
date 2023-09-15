<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Captcha;

/**
 * Alpha captcha to validate a consonant.
 */
class ConsonantCaptcha extends AbstractAlphaCaptcha
{
    private const CONSONANT = 'BCDFGHJKLMNPQRSTVWXZ';

    private const INDEX_MAPPING = [
        '0' => 'first',
        '1' => 'second',
        '2' => 'third',
        '-1' => 'last',
    ];

    /**
     * Gets the default index name.
     */
    public static function getDefaultIndexName(): string
    {
        return 'ConsonantCaptcha';
    }

    protected function getAnswer(string $word, int $letterIndex): string
    {
        return $this->findAnswer($word, $letterIndex, self::CONSONANT);
    }

    protected function getLetterIndex(): int
    {
        return \array_rand(self::INDEX_MAPPING);
    }

    protected function getQuestion(string $word, int $letterIndex): string
    {
        $params = [
            '%index%' => $this->trans(self::INDEX_MAPPING[$letterIndex], [], 'captcha'),
            '%letter%' => $this->trans('consonant', [], 'captcha'),
            '%word%' => $word,
        ];

        return $this->trans('sentence', $params, 'captcha');
    }
}
