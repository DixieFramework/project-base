<?php

declare(strict_types=1);

namespace Talav\WebBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Talav\Component\User\Model\UserInterface;

class ThemeEvent extends Event
{
    public const JAVASCRIPT = 'app.theme.javascript';
    public const STYLESHEET = 'app.theme.css';
    public const HTML_HEAD = 'app.theme.html_head';
    public const CONTENT_BEFORE = 'app.theme.content_before';
    public const CONTENT_START = 'app.theme.content_start';
    public const CONTENT_END = 'app.theme.content_end';
    public const CONTENT_AFTER = 'app.theme.content_after';

    /**
     * @var UserInterface|null
     */
    private $user;
    /**
     * @var string
     */
    private $content = '';
    /**
     * @var mixed
     */
    protected $payload;

    public function __construct(?UserInterface $user = null, $payload = null)
    {
        $this->user = $user;
        $this->payload = $payload;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function addContent(string $content): ThemeEvent
    {
        $this->content .= $content;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @param mixed $payload
     * @return ThemeEvent
     */
    public function setPayload($payload)
    {
        $this->payload = $payload;

        return $this;
    }
}
