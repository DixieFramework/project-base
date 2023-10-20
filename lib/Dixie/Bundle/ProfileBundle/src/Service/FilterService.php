<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Service;

use Talav\Component\Resource\Manager\ManagerInterface;
use Talav\Component\User\Model\UserInterface;
use Talav\Component\User\Repository\UserRepositoryInterface;
use Talav\ProfileBundle\Entity\Filter;
use Talav\ProfileBundle\Entity\ProfileInterface;
use Talav\ProfileBundle\Repository\FilterRepository;
use Doctrine\ORM\EntityManagerInterface;

class FilterService
{
    private EntityManagerInterface $entityManager;
    private UserRepositoryInterface $userRepository;
    private FilterRepository $filterRepository;
	private ManagerInterface $profileManager;

	public function __construct(
        EntityManagerInterface $entityManager,
		ManagerInterface $profileManager,
        UserRepositoryInterface $userRepository,
        FilterRepository $filterRepository
    ) {
        $this->userRepository = $userRepository;
		$this->profileManager = $profileManager;
		$this->entityManager = $entityManager;
        $this->filterRepository = $filterRepository;
	}

    public function create($profileId)
    {
        $filter = $this->filterRepository->find($profileId);

        if ($filter === null) {
            $profile = $this->profileManager->getRepository()->find($profileId);
            $filter = new Filter();
            $filter->setProfile($profile);
            $this->entityManager->persist($filter);
            return $filter;
        }

        return $filter;
    }
}
