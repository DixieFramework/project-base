<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Mime;

use Symfony\Component\Mime\Header\Headers;
use Symfony\Component\Mime\Part\AbstractPart;

/**
 * Notification email to reset password.
 */
class ResetPasswordEmail extends NotificationEmail
{
    public function __construct(Headers $headers = null, AbstractPart $body = null)
    {
        parent::__construct($headers, $body);
        $this->htmlTemplate('@TalavUser/notification/reset_password.html.twig');
    }
}
