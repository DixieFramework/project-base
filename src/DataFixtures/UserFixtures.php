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
    private const PASS = '123456';

    private $security;

    private $roleService;

    private const ROLES = [
        RoleFixtures::ROLE_USER,
        RoleFixtures::ROLE_MODERATOR,
        RoleFixtures::ROLE_SUPER_MODERATOR,
        RoleFixtures::ROLE_ADMIN,
        RoleFixtures::ROLE_SUPER_ADMIN,
        RoleFixtures::ROLE_ROOT,
        RoleFixtures::ROLE_DEVELOPER,
    ];

	const ROLE_USER             = 0;
	const ROLE_MODERATOR        = 1;
	const ROLE_SUPER_MODERATOR  = 2;
	const ROLE_ADMIN            = 3;
	const ROLE_SUPER_ADMIN      = 4;
	const ROLE_ROOT             = 8;
	const ROLE_DEVELOPER        = 9;

	const ROLE_TYPES = [
		self::ROLE_USER             => 'ROLE_USER',
		self::ROLE_MODERATOR        => 'ROLE_MODERATOR',
		self::ROLE_SUPER_MODERATOR  => 'ROLE_SUPER_MODERATOR',
		self::ROLE_ADMIN            => 'ROLE_ADMIN',
		self::ROLE_SUPER_ADMIN      => 'ROLE_SUPER_ADMIN',
		self::ROLE_ROOT             => 'ROLE_ROOT',
		self::ROLE_DEVELOPER        => 'ROLE_DEVELOPER',
	];

    public const EMAIL_ROOT = 'root@example.com';

    public const EMAIL_SUPER_ADMIN = 'super-admin@example.com';

    public const EMAIL_ADMIN = 'admin@example.com';

    public const EMAIL_SUPER_MODERATOR = 'super-moderator@example.com';

    public const EMAIL_MODERATOR = 'moderator@example.com';

    public const EMAIL_USER = 'user@example.com';

    public const EMAIL_DEVELOPER = 'developer@example.com';

    public function __construct(
        private readonly UserManagerInterface $userManager,
        private readonly ManagerInterface     $profileManager
    )
    {
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

        $user = $this->createUser($manager, Roles::developer(), self::EMAIL_DEVELOPER);
        $this->addReference(self::EMAIL_DEVELOPER, $user);
    }

    private function createUser(ObjectManager $manager, Roles|array $roles, string $email)
    {
        $user = $this->userManager->create();

        $user
//            ->setPassword($this->passwordEncoder->encodePassword(
//                $user,
//                self::PASS
//            ))
            ->setUsername(current(explode('@', $email)))
            ->setPlainPassword(self::PASS)
            ->setRoles($roles)
            ->setEnabled(true)
            ->setVerified(true)
            ->setEmail($email)
        ;

	    $this->userManager->updateCanonicalFields($user);
	    $this->userManager->updatePassword($user);

        if (!$user->getProfile()) {
            /** @var ProfileInterface $profile */
            $profile = $this->profileManager->create();
            $profile->setFirstName('John');
            $profile->setLastName('Doe');
            $profile->setGender(Gender::X);
            $profile->setBirthdate(\DateTime::createFromFormat('j-M-Y', '01-Jan-1970'));

            $user->setProfile($profile);
        }

		$userType = strtoupper(str_replace('-', '_', current(explode('@', $email))));
	    $roles = (new \ReflectionClass(self::class))->getConstants();
		if (in_array('ROLE_' . $userType, $roles['ROLES'])) {
			$userRole = $this->getReference('ROLE_' . $userType);
			$user->sync('userRoles', new ArrayCollection([$userRole]));
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
