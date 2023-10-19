<?php

declare(strict_types=1);

namespace Talav\WebBundle;

/**
 * Contains all events thrown in the TalavWebBundle.
 */
final class TalavWebEvents
{
    /**
     * The talav.build_embeddable_js event is dispatched to allow plugins to extend the talav tracking js.
     *
     * The event listener receives a Talav\WebBundle\Event\BuildJsEvent instance.
     *
     * @var string
     */
    public const BUILD_TALAV_JS = 'talav_web.build_embeddable_js';
}
