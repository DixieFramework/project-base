<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Captcha;

/**
 * Alpha captcha to validate a letter.
 */
class LetterCaptcha extends AbstractAlphaCaptcha
{
    private const INDEX_MAPPING = [
        '0' => 'first',
        '1' => 'second',
        '2' => 'third',
        '3' => 'fourth',
        '4' => 'fifth',
        '-1' => 'last',
    ];

    /**
     * Gets the default index name.
     */
    public static function getDefaultIndexName(): string
    {
        return 'LetterCaptcha';
    }

    protected function getAnswer(string $word, int $letterIndex): string
    {
        if (0 > $letterIndex) {
            $letterIndex = \abs($letterIndex) - 1;
            $word = \strrev($word);
        }

        return $word[$letterIndex];
    }

    protected function getLetterIndex(): int
    {
        return \array_rand(self::INDEX_MAPPING);
    }

    protected function getQuestion(string $word, int $letterIndex): string
    {
        $params = [
            '%index%' => $this->trans(self::INDEX_MAPPING[$letterIndex], [], 'captcha'),
            '%letter%' => $this->trans('letter', [], 'captcha'),
            '%word%' => $word,
        ];

        return $this->trans('sentence', $params, 'captcha');
    }
}
