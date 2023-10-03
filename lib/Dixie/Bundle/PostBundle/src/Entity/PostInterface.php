<?php

declare(strict_types=1);

namespace Talav\PostBundle\Entity;

use Talav\Component\Resource\Model\ResourceInterface;

interface PostInterface extends ResourceInterface
{
    public function getContent(): ?string;

    public function setContent(?string $content): self;

    public function getDescription(): ?string;

    public function setDescription(?string $description): self;

    public function getStatus(): ?bool;

    public function setStatus(?bool $status): self;

    public function getSlug(): ?string;

    public function setSlug(string $slug): self;
}
