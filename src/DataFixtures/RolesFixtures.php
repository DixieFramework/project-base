<?php

declare(strict_types=1);

namespace Groshy\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Groshy\Entity\Role;
use Groshy\Entity\User;

class RolesFixtures extends Fixture implements DependentFixtureInterface
{
    public const SuperAdministrator_ROLE_REFERENCE = 'SuperAdministrator';
    public const Administrator_ROLE_REFERENCE = 'Administrator';
	public const SuperModerator_ROLE_REFERENCE = 'SuperModerator';
	public const Moderator_ROLE_REFERENCE = 'Moderator';
	public const User_ROLE_REFERENCE = 'User';

    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function load(ObjectManager $manager)
    {
        $superAdministratorRole = $this->findOrCreate('ROLE_SUPER_ADMIN');
        $adminRole              = $this->findOrCreate('ROLE_ADMIN');
        $superModeratorRole     = $this->findOrCreate('ROLE_SUPER_MODERATOR');
        $moderatorRole          = $this->findOrCreate('ROLE_MODERATOR');
        $userRole               = $this->findOrCreate('ROLE_USER');

        /** @var User $adminUser */
        $adminUser = $this->getReference(UserFixtures::ADMIN_USER_REFERENCE);
        $adminUser->sync('roles', new ArrayCollection([$superAdministratorRole]));

        $this->entityManager->persist($adminUser);
        $this->entityManager->flush();

        $this->addReference(self::SuperAdministrator_ROLE_REFERENCE, $superAdministratorRole);
        $this->addReference(self::Administrator_ROLE_REFERENCE, $adminRole);
	    $this->addReference(self::SuperModerator_ROLE_REFERENCE, $superModeratorRole);
	    $this->addReference(self::Moderator_ROLE_REFERENCE, $moderatorRole);
	    $this->addReference(self::User_ROLE_REFERENCE, $userRole);
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class
        ];
    }

    private function findOrCreate(string $roleName): Role
    {
        $role = $this->entityManager
            ->getRepository(Role::class)
            ->findOneBy(['name' => $roleName]);

        if (!$role) {
            $role = (new Role())->setName($roleName);
            $this->entityManager->persist($role);
            $this->entityManager->flush();
        }

        return $role;
    }

}