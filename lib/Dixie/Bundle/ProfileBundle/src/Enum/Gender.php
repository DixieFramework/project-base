<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Enum;

use Talav\CoreBundle\Enums\Traits\EnumToArrayTrait;
use Talav\CoreBundle\Enums\Traits\EnumTrait;

enum Gender: string
{
	use EnumToArrayTrait;

    case Male = 'male';
    case Female = 'female';
    case X = 'x';

	public static function list(): array
	{
		$list = [];
		foreach (self::cases() as $status) {
			$list[] = $status->value;
		}

		return $list;
	}
}
