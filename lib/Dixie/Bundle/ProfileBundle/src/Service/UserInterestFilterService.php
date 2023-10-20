<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Service;

use Talav\Component\Resource\Manager\ManagerInterface;
use Talav\Component\User\Repository\UserRepositoryInterface;
use Talav\ProfileBundle\Entity\Interest;
use Talav\ProfileBundle\Entity\UserInterestFilter;
use Talav\ProfileBundle\Repository\InterestRepository;
use Talav\ProfileBundle\Repository\UserInterestFilterRepository;
use Talav\ProfileBundle\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Uid\Uuid;

class UserInterestFilterService
{
    private EntityManagerInterface $entityManager;
    private UserRepositoryInterface $userRepository;
    private UserInterestFilterRepository $userInterestFilterRepository;
    private InterestRepository $interestRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
		private readonly ManagerInterface $profileManager,
        UserRepositoryInterface $userRepository,
        UserInterestFilterRepository $userInterestFilterRepository,
        InterestRepository $interestRepository
    ) {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->userInterestFilterRepository = $userInterestFilterRepository;
        $this->interestRepository = $interestRepository;
    }

    /**
     * @param mixed      $profileId
     * @param Interest[] $interests
     */
    public function createUserInterestFilters($profileId, array $interests): void
    {
        $this->entityManager->wrapInTransaction(function ($entityManager) use ($profileId, $interests) {
            $user = $this->profileManager->getRepository()->find($profileId);

            if ($user === null) {
                throw new Exception('Missing user');
            }

            $this->userInterestFilterRepository->deleteByUserId($user->getId());

            foreach ($interests as $interest) {
                $userInterestFilter = new UserInterestFilter();
                $userInterestFilter->setProfile($user);
                $userInterestFilter->setInterest($interest);
                $entityManager->persist($userInterestFilter);
            }
        });
    }

    public function findByProfileId(mixed $profileId): array
    {
        return $this->userInterestFilterRepository->findInterestFiltersByProfileId($profileId);
    }

    /**
     * @internal Provided for behat test setup
     *
     * @throws Exception
     */
    public function createUserInterestFilterByNames(Uuid $userId, array $interestNames): void
    {
        $this->entityManager->wrapInTransaction(function ($entityManager) use ($userId, $interestNames) {
            $user = $this->userRepository->find($userId);

            if ($user === null) {
                throw new Exception('Missing user');
            }

            $this->userInterestFilterRepository->deleteByUserId($user->getId());

            foreach ($interestNames as $interestName) {
                $interest = $this->interestRepository->findOneBy(['name' => $interestName]);

                if ($interest === null) {
                    throw new Exception(sprintf('Could not find interest [%s] by name', $interestName));
                }

                $userInterestFilter = new UserInterestFilter();
                $userInterestFilter->setProfile($user);
                $userInterestFilter->setInterest($interest);
                $entityManager->persist($userInterestFilter);
            }
        });
    }
}
