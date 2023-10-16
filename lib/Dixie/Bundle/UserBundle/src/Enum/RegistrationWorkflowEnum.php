<?php

declare(strict_types=1);

namespace Talav\UserBundle\Enum;

enum RegistrationWorkflowEnum: string
{
    case STATE_PERSONAL_INFO = 'personal_information';
    case STATE_ACCOUNT_INFO = 'account_information';
    case STATE_CAPTCHA = 'captcha';
    case STATE_COMPLETE = 'complete';

    case TRANSITION_TO_PERSONAL_INFO_FORM = 'to_personal';
    case TRANSITION_TO_ACCOUNT_FORM = 'to_account';
    case TRANSITION_TO_CAPTCHA = 'to_captcha';
    case TRANSITION_TO_COMPLETE = 'to_complete';
}
