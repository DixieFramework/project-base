<?php

declare(strict_types=1);

namespace Groshy\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Groshy\Entity\Role;

class RoleFixtures extends Fixture
{

    public const ROLE_ROOT = 'ROLE_ROOT';

    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    public const ROLE_ADMIN = 'ROLE_ADMIN';

    public const ROLE_SUPER_MODERATOR = 'ROLE_SUPER_MODERATOR';

    public const ROLE_MODERATOR = 'ROLE_MODERATOR';

    public const ROLE_USER = 'ROLE_USER';

    public const ROLE_DEVELOPER = 'ROLE_DEVELOPER';

    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function load(ObjectManager $manager)
    {
        $roles = (new \ReflectionClass(self::class))->getConstants();

        foreach ($roles as $role) {
            $userRole = $this->findOrCreate($role);
        }
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

            $this->addReference($roleName, $role);

            $this->entityManager->persist($role);
            $this->entityManager->flush();
        }

        return $role;
    }
}
