<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Traits;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Service\Attribute\Required;

trait EntityManagerAwareTrait
{
    protected EntityManagerInterface $entityManager;

	#[Required]
    public function setEntityManager(EntityManagerInterface $entityManager): void
    {
        $this->entityManager = $entityManager;
    }
}
