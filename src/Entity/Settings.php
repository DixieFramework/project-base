<?php

declare(strict_types=1);

namespace Groshy\Entity;

use Doctrine\ORM\Mapping as ORM;
use Talav\SettingsBundle\Entity\Settings as TalavSettings;

#[ORM\Entity]
#[ORM\Table(name: 'talav_settings')]
class Settings extends TalavSettings
{
}
