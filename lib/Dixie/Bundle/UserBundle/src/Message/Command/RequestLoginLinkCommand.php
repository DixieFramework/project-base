<?php

declare(strict_types=1);

namespace Talav\UserBundle\Message\Command;

use Symfony\Component\Validator\Constraints as Assert;

final class RequestLoginLinkCommand
{
    public function __construct(
        #[Assert\NotBlank] #[Assert\Email] public ?string $email = null
    ) {
    }
}
