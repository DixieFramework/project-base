<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Entity;

use Talav\Component\Resource\Model\ResourceInterface;
use Talav\ProfileBundle\Entity\ProfileInterface;
use Talav\UserBundle\Model\UserInterface;

interface ReportInterface extends ResourceInterface
{
    public function getProfile(): ?ProfileInterface;

    public function setProfile(?ProfileInterface $user): self;
}
