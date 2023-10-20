<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Doctrine\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait UserIdTrait
{
    #[ORM\Column(type: Types::STRING, length: 36, nullable: true)]
    #[ORM\Column(type: 'string', length: 36, nullable: true)]
    protected ?string $user_id = null;

    /**
     * @param int|string|object $user_id
     */
    public function setUserId($user_id): self
    {
        if (is_object($user_id)) {
            if (method_exists($user_id, 'getId')) {
                $user_id = $user_id->getId();
            } else {
                $user_id = (string) $user_id;
            }
        }

        if (empty($user_id)) {
            $user_id = null;
        }

        $this->user_id = $user_id;

        return $this;
    }

    public function getUserId(): ?string
    {
        return $this->user_id;
    }
}
