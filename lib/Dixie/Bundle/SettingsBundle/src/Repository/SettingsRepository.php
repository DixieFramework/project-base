<?php

declare(strict_types=1);

namespace Talav\SettingsBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Talav\Component\Resource\Repository\ResourceRepository;
use Talav\SettingsBundle\Context\ScopeContextInterface;
use Talav\Component\Resource\Repository\RepositoryPaginatorTrait;
use Talav\SettingsBundle\Model\SettingsOwnerInterface;

//class SettingsRepository extends EntityRepository implements SettingsRepositoryInterface
class SettingsRepository extends ResourceRepository implements SettingsRepositoryInterface
{
    use RepositoryPaginatorTrait;

    /**
     * {@inheritdoc}
     */
    public function removeAllByScope(string $scope): void
    {
        $qb = $this->createQueryBuilder('s')
            ->delete()
            ->where('s.scope = :scope')
            ->setParameter('scope', $scope);

        $qb->getQuery()->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function findByScopeAndOwner(string $scope, SettingsOwnerInterface $settingsOwner): QueryBuilder
    {
        $qb = $this->createQueryBuilder('s')
            ->andWhere('s.scope = :scope')
            ->andWhere('s.owner = :owner')
            ->setParameter('scope', $scope)
            ->setParameter('owner', $settingsOwner);

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function findAllByScopeAndOwner(ScopeContextInterface $scopeContext): QueryBuilder
    {
        $qb = $this->createQueryBuilder('s')
            ->andWhere('s.scope = :globalScope')
            ->setParameter('globalScope', ScopeContextInterface::SCOPE_GLOBAL);

        /* @var array $scope */
        foreach ($scopeContext->getScopesOwners() as $scopeName => $owner) {
            $qb->orWhere(
                $qb->expr()->andX(
                    $qb->expr()->eq('s.scope', ':scope_'.$scopeName),
                    $qb->expr()->eq('s.owner', ':owner_'.$scopeName)
                )
            )
            ->setParameter('scope_'.$scopeName, $scopeName)
            ->setParameter('owner_'.$scopeName, $owner->getId());
        }

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByNameAndScopeAndOwner(string $name, string $scope, SettingsOwnerInterface $owner = null): QueryBuilder
    {
        $qb = $this->createQueryBuilder('s')
            ->andWhere('s.scope = :scope')
            ->setParameter('scope', $scope)
            ->andWhere('s.name = :name')
            ->setParameter('name', $name);

        if (null !== $scope && null !== $owner) {
            $qb
                ->andWhere('s.owner = :owner')
                ->setParameter('owner', $owner->getId())
            ;
        }

        return $qb;
    }
}
