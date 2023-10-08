<?php

declare(strict_types=1);

namespace Talav\AvatarBundle\Model;

use Talav\Component\Media\Model\MediaInterface;

interface UserCoverInterface
{
    public function getCover(): ?MediaInterface;

    public function setCover(?MediaInterface $avatar): void;

    public function getCoverName(): ?string;

    public function getCoverDescription(): ?string;
}
