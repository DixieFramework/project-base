<?php

declare(strict_types=1);

namespace Talav\WebBundle\Twig\Component;

use Symfony\Component\Form\FormView;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * Class Form.
 */
#[AsTwigComponent]
final class TalavForm
{
    public FormView $form;
}
