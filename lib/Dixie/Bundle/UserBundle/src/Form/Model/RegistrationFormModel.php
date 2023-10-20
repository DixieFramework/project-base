<?php

declare(strict_types=1);

namespace Talav\UserBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Talav\UserBundle\Validator\Constraints\RegisteredUser;

final class RegistrationFormModel
{
    /**
     * @RegisteredUser(message="talav.email.already_used")
     */
    #[Assert\NotBlank(message: 'talav.email.blank')]
    #[Assert\Email(message: 'talav.email.invalid', mode: 'strict')]
    public ?string $email = null;

    /**
     * @RegisteredUser(message="talav.username.already_used", field="username")
     */
    #[Assert\NotBlank(message: 'talav.username.blank')]
    public ?string $username = null;

    #[Assert\NotBlank(message: 'talav.password.blank')]
    #[Assert\Length(min: 4, minMessage: 'talav.password.short', max: 254, maxMessage: 'talav.password.long')]
    public ?string $password = null;
}
