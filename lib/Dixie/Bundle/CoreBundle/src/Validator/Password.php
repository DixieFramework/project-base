<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Password constraint.
 *
 * @Annotation
 *
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Password extends Constraint
{
    final public const CASE_DIFF_ERROR = '4c725240-da48-42df-ba9a-ce09a16ab1b5';

    final public const EMAIL_ERROR = '85386dde-1b29-42d4-9b7c-de03693fb963';

    final public const LETTERS_ERROR = 'cc369ec9-ea3d-4d27-8f96-6e03bfb63323';

    final public const NUMBERS_ERROR = '902a620e-8cf9-42bd-9219-3938c3fea0c5';

    final public const PWNED_ERROR = 'd042c39d-b2d3-4ef3-97b8-10948aed2988';

    final public const SPECIAL_CHAR_ERROR = '5c5998ca-d67b-45ed-b210-dda950c8ea09';

    protected const ERROR_NAMES = [
        self::CASE_DIFF_ERROR => 'CASE_DIFF_ERROR',
        self::EMAIL_ERROR => 'EMAIL_ERROR',
        self::LETTERS_ERROR => 'LETTERS_ERROR',
        self::NUMBERS_ERROR => 'NUMBERS_ERROR',
        self::PWNED_ERROR => 'PWNED_ERROR',
        self::SPECIAL_CHAR_ERROR => 'SPECIAL_CHAR_ERROR',
    ];

    /**
     * Add all violations or stop of the first violation found.
     */
    public bool $all = false;

    /**
     * Checks if the password contains upper and lower characters.
     */
    public bool $case_diff = false;

    /**
     * Case diff error message.
     */
    public string $case_diff_message = 'password.case_diff';

    /**
     * Checks if the password is an e-mail.
     */
    public bool $email = false;

    /**
     * Email error message.
     */
    public string $email_message = 'password.email';

    /**
     * Checks if the password contains letters.
     */
    public bool $letters = true;

    /**
     * Letters error message.
     */
    public string $letters_message = 'password.letters';

    /**
     * Checks if the password contains numbers.
     */
    public bool $numbers = false;

    /**
     * Numbers error message.
     */
    public string $numbers_message = 'password.numbers';

    /**
     * Checks if the password is compromised.
     */
    public bool $pwned = false;

    /**
     *  Password comprise error message.
     */
    public string $pwned_message = 'password.pwned';

    /**
     * Checks if the password contains special characters.
     */
    public bool $special_char = false;

    /**
     * Special char error message.
     */
    public string $special_char_message = 'password.special_char';
}
