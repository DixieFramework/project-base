<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Repository;

use Doctrine\ORM\Query\Expr\Join;
use Talav\Component\Resource\Repository\ResourceRepository;
use Talav\Component\User\Model\UserInterface;
use Talav\ProfileBundle\Entity\ProfileInterface;
use Talav\UserBundle\Entity\User;

class ProfileRepository extends ResourceRepository
{
    public function findByProfileInfoStartsWith($term)
    {
        $qb = $this->createQueryBuilder('p');

        return $qb
            ->leftJoin(
                UserInterface::class,
                'u',
                Join::WITH,
                'u.profile = p.id'
            )
//            ->leftJoin(User::class, 'u', Join::WITH, 'u = p.user')
            ->where($qb->expr()->like('u.username.username', $qb->expr()->literal($term . '%')))
//            ->setParameter('term', $term . '%')
//            ->andWhere('u.status in (:status)')
//            ->setParameter(':status', 1)
            ->setMaxResults(20)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $keyword
     * @param ProfileInterface|null $profile
     * @param bool $onlineOnly
     * @return ProfileInterface|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findLikeName($keyword, ProfileInterface $profile = NULL, $onlineOnly = false)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->leftJoin('p.user', 'u');
        $qb->where($qb->expr()->like('u.username', $qb->expr()->literal($keyword . '%')));
        if ($onlineOnly) {
            $qb->andWhere('p.currentResourceId IS NOT NULL');
        }
        if ($profile instanceof \Talav\ProfileBundle\Entity\ProfileInterface) {
            $qb->andWhere('p.id != :profileId');
            $qb->setParameter('profileId', $profile->getId());
        }
        $qb->setMaxResults(1);
        return $qb->getQuery()->getOneOrNullResult();
    }

	/**
	 * Get matches by complex filter criteria -
	 * a custom query based on various parameters passed
	 *
	 * @param $params
	 * @return array
	 */
	public function findFiltered($params)
	{
		$query = $this->getEntityManager()->createQueryBuilder()
			->select('p as profile, u.username.username as username, TIMESTAMP_DIFF (YEAR, p.birthdate, NOW()) as age')
			->from(ProfileInterface::class, 'p')
			->leftJoin('p.user', 'u')
			->orderBy('u.usernameCanonical', 'DESC');

		if (key_exists('min_age', $params) && !is_null($params['min_age'])) {
			$query->andWhere('TIMESTAMP_DIFF (YEAR, p.birthdate, NOW()) >= :min_age')
				->setParameter('min_age', $params['min_age']);
		}

		if (key_exists('max_age', $params) && !is_null($params['max_age'])) {
			$query->andWhere('TIMESTAMP_DIFF (YEAR, p.birthdate, NOW()) <= :max_age')
				->setParameter('max_age', $params['max_age']);
		}

		if (key_exists('interests', $params) && !is_null($params['interests'])) {
			$query->andWhere('p.interests = :interests')
				->setParameter('interests', $params['interests']);
		}

		if (key_exists('username', $params) && !is_null($params['username'])) {
			$query->andWhere('u.usernameCanonical LIKE :username')
				->setParameter('username', '%' . $params['username'] . '%');
		}

		return $query->getQuery()->getResult();
	}
}
