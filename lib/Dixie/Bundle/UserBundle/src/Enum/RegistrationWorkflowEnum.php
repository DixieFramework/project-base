<?php

declare(strict_types=1);

namespace Talav\UserBundle\Enum;

enum RegistrationWorkflowEnum: string
{
    case STATE_PERSONAL_INFO = 'personal_information';
    case STATE_PAYMENT_INFO = 'payment_information';
    case STATE_CAPTCHA = 'captcha';
    case STATE_COMPLETE = 'complete';

    case TRANSITION_TO_PERSONAL_INFO_FORM = 'to_personal';
    case TRANSITION_TO_PAYMENT_FORM = 'to_payment';
    case TRANSITION_TO_CAPTCHA = 'to_captcha';
    case TRANSITION_TO_COMPLETE = 'to_complete';
}
