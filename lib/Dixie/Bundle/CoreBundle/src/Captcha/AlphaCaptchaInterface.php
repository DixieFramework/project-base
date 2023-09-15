<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Captcha;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * Class implementing this interface deals with question and answer validation.
 */
#[AutoconfigureTag]
interface AlphaCaptchaInterface
{
    /**
     * Checks if the given answer is correct again the expected answer.
     */
    public function checkAnswer(string $givenAnswer, string $expectedAnswer): bool;

    /**
     * Gets the challenge.
     *
     * @return string[] the question and the answer
     */
    public function getChallenge(): array;

    /**
     * Gets the default index name.
     */
    public static function getDefaultIndexName(): string;
}
