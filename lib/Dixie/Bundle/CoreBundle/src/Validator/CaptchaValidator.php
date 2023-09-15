<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Validator;

use Talav\CoreBundle\Service\CaptchaImageService;
use Symfony\Component\Validator\Constraint;

/**
 * Captcha constraint validator.
 *
 * @extends AbstractConstraintValidator<Captcha>
 */
class CaptchaValidator extends AbstractConstraintValidator
{
    /**
     * Constructor.
     */
    public function __construct(private readonly CaptchaImageService $service)
    {
        parent::__construct(Captcha::class);
    }

    /**
     * @param Captcha $constraint
     */
    protected function doValidate(string $value, Constraint $constraint): void
    {
        if (!$this->service->validateTimeout()) {
            $this->context->buildViolation($constraint->timeout_message)
                ->setCode(Captcha::IS_TIMEOUT_ERROR)
                ->addViolation();
        } elseif (!$this->service->validateToken($value)) {
            $this->context->buildViolation($constraint->invalid_message)
                ->setCode(Captcha::IS_INVALID_ERROR)
                ->addViolation();
        }
    }
}
