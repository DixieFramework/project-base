<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Validator;

use Talav\CoreBundle\Service\RecaptchaService;
use Symfony\Component\Validator\Constraint;

/**
 * Google reCaptcha constraint validator.
 *
 * @extends AbstractConstraintValidator<Recaptcha>
 */
class RecaptchaValidator extends AbstractConstraintValidator
{
    /**
     * Constructor.
     */
    public function __construct(private readonly ReCaptchaService $service)
    {
        parent::__construct(Recaptcha::class);
    }

    /**
     * @param Recaptcha $constraint
     */
    protected function doValidate(string $value, Constraint $constraint): void
    {
        $response = $this->service->verify($value);
        if ($response->isSuccess()) {
            return;
        }

        /** @var string[] $errorCodes */
        $errorCodes = $response->getErrorCodes();
        foreach ($errorCodes as $code) {
            $this->context->buildViolation("recaptcha.$code")
                ->setCode($code)
                ->addViolation();
        }
    }
}
