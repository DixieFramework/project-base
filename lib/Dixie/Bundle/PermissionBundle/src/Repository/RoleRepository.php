<?php

declare(strict_types=1);

namespace Talav\PermissionBundle\Repository;

use Doctrine\ORM\Exception\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Talav\Component\Resource\Repository\ResourceRepository;
use Talav\PermissionBundle\Entity\RoleInterface;

/**
 * Class RoleRepository
 * @package Talav\UserBundle\Repository
 */
class RoleRepository extends ResourceRepository implements RoleRepositoryInterface
{
	/**
	 * @return RoleInterface[]
	 */
	public function findAll()
	{
		return parent::findAll();
	}

	public function save(object $entity, bool $andFlush = true): void
	{
		$this->_em->persist($entity);

		if ($andFlush) {
			$this->getEntityManager()->flush();
		}
	}

	public function remove(object $entity): void
	{
		$this->_em->beginTransaction();

		try {
			$this->_em->remove($entity);
			$this->_em->flush();
			$this->_em->commit();
		} catch (ORMException $ex) {
			$this->_em->rollback();
			throw $ex;
		}
	}

    public function getByPermission(string $permission): array
    {
        return $this->createQueryBuilder('r')
            ->join('r.permissions', 'p')
            ->where('p.name = :permission')
            ->setParameter('permission', $permission)
            ->getQuery()
            ->execute();
    }
}
