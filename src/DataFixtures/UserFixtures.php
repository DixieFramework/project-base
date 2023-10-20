<?php

declare(strict_types=1);

namespace Groshy\DataFixtures;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Talav\Component\Resource\Manager\ManagerInterface;
use Talav\Component\User\Manager\UserManagerInterface;
use Talav\Component\User\ValueObject\Roles;
use Talav\ProfileBundle\Entity\ProfileInterface;
use Talav\ProfileBundle\Enum\Gender;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    private const PASS = 'qwerty';

    private $passwordEncoder;

    private $security;

    private $roleService;

    private const ROLES = [
        RoleFixtures::ROLE_USER,
        RoleFixtures::ROLE_MODERATOR,
        RoleFixtures::ROLE_SUPER_MODERATOR,
        RoleFixtures::ROLE_ADMIN,
        RoleFixtures::ROLE_SUPER_ADMIN,
        RoleFixtures::ROLE_ROOT,
        RoleFixtures::ROLE_DEV,
    ];

    public const EMAIL_ROOT = 'root@example.com';

    public const EMAIL_SUPER_ADMIN = 'super-admin@example.com';

    public const EMAIL_ADMIN = 'admin@example.com';

    public const EMAIL_SUPER_MODERATOR = 'super-moderator@example.com';

    public const EMAIL_MODERATOR = 'moderator@example.com';

    public const EMAIL_USER = 'user@example.com';

    public const EMAIL_DEV = 'dev@example.com';

    public function __construct(
        private readonly UserManagerInterface $userManager,
        private readonly ManagerInterface     $profileManager,
        UserPasswordHasherInterface $passwordEncoder
    )
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = $this->createUser($manager, Roles::superAdmin(), self::EMAIL_SUPER_ADMIN);
        $this->addReference(self::EMAIL_SUPER_ADMIN, $user);

        $user = $this->createUser($manager, Roles::admin(), self::EMAIL_ADMIN);
        $this->addReference(self::EMAIL_ADMIN, $user);

        $user = $this->createUser($manager, Roles::admin(), self::EMAIL_SUPER_MODERATOR);
        $this->addReference(self::EMAIL_SUPER_MODERATOR, $user);

        $user = $this->createUser($manager, Roles::admin(), self::EMAIL_MODERATOR);
        $this->addReference(self::EMAIL_MODERATOR, $user);

        $user = $this->createUser($manager, Roles::developer(), self::EMAIL_USER);
        $this->addReference(self::EMAIL_USER, $user);

        $user = $this->createUser($manager, Roles::developer(), self::EMAIL_DEV);
        $this->addReference(self::EMAIL_DEV, $user);
    }

    private function createUser(ObjectManager $manager, Roles|array $roles, string $email)
    {
        $user = $this->userManager->create();

        $user
//            ->setPassword($this->passwordEncoder->encodePassword(
//                $user,
//                self::PASS
//            ))
            ->setUsername(current(explode('.', $email)))
            ->setPlainPassword(self::PASS)
            ->setRoles($roles)
            ->setStatus(1)
            ->setVerified(1)
            ->setEmail($email)
        ;


        if (!$user->getProfile()) {
            /** @var ProfileInterface $profile */
            $profile = $this->profileManager->create();
            $profile->setFirstName('John');
            $profile->setLastName('Doe');
            $profile->setGender(Gender::X);
            $profile->setBirthdate(\DateTime::createFromFormat('j-M-Y', '01-Jan-1970'));

            $user->setProfile($profile);

        }

        $this->userManager->update($user, true);
//        $manager->persist($user);
//        $manager->flush();

        return $user;
    }

    public function getDependencies()
    {
        return [
            PermissionsFixtures::class,
            RoleFixtures::class,
        ];
    }
}
