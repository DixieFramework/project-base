<?php

declare(strict_types=1);

namespace Talav\PermissionBundle\Repository;

use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NoResultException;
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

    public function findRoleById($id)
    {
        try {
            $queryBuilder = $this->createQueryBuilder('role')
                ->leftJoin('role.permissions', 'permissions')
                ->addSelect('permissions')
                ->where('role.id=:roleId');

            $query = $queryBuilder->getQuery();
            $query->setParameter('roleId', $id);

            return $query->getSingleResult();
        } catch (NoResultException $e) {
            return;
        }
    }

    public function findAllRoles(array $filters = [])
    {
        try {
            $queryBuilder = $this->createQueryBuilder('role')
                ->leftJoin('role.permissions', 'permissions')
                ->addSelect('permissions');

            if (isset($filters['system'])) {
                $queryBuilder->andWhere('role.system = :roleSystem')
                    ->setParameter('roleSystem', $filters['system']);
            }

            $query = $queryBuilder->getQuery();

            return $query->getResult();
        } catch (NoResultException $e) {
            return;
        }
    }

    public function getRoleNames()
    {
        $query = $this->createQueryBuilder('role')
            ->select('role.name')
            ->getQuery();

        $roles = [];
        foreach ($query->getArrayResult() as $roleEntity) {
            $roles[] = $roleEntity['name'];
        }

        return $roles;
    }
}
