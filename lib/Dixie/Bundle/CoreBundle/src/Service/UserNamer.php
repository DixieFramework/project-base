<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Service;

use Talav\CoreBundle\Entity\User;
use Talav\CoreBundle\Enums\ImageExtension;
use Talav\CoreBundle\Enums\ImageSize;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\NamerInterface;

/**
 * Namer for user images.
 *
 * @implements NamerInterface<User>
 */
#[Autoconfigure(public: true)]
class UserNamer implements NamerInterface
{
    /**
     * Gets the base file name.
     */
    public static function getBaseName(User|int $key, ImageSize $size, ImageExtension|string $ext = null): string
    {
        $id = \is_int($key) ? $key : (int) $key->getId();
        $name = \sprintf('USER_%06d_%03d', $id, $size->value);
        if (null !== $ext && '' !== $ext) {
            if ($ext instanceof ImageExtension) {
                $ext = $ext->value;
            }

            return "$name.$ext";
        }

        return $name;
    }

    public function name($object, PropertyMapping $mapping): string
    {
        return self::getBaseName($object, ImageSize::DEFAULT, ImageExtension::PNG);
    }
}
