<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Enum;

enum FriendStatus
{
    case AWAITING_CONFIRMATION;
    case CONFIRMED;
}
