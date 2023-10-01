<?php

declare(strict_types=1);

namespace Talav\CoreBundle\DataTable\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Talav\CoreBundle\DataTable\ActionRenderer;
use Talav\CoreBundle\DataTable\DataTableRenderer;

class DataTableExtension extends AbstractExtension
{
    public function __construct(protected DataTableRenderer $renderer, protected ActionRenderer $actionRenderer)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('render_table', [$this->renderer, 'render'], [
                'is_safe' => ['html'],
            ]),
            new TwigFunction('render_action', [$this->actionRenderer, 'renderAction'], [
                'is_safe' => ['html'],
            ])
        ];
    }
}
