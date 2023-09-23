<?php

declare(strict_types=1);

namespace Groshy\DataFixtures;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Talav\Component\User\Manager\UserManagerInterface;
use Talav\Component\User\Message\Command\CreateUserCommand;
use Talav\Component\User\Message\Dto\CreateUserDto;

final class UserFixtures extends BaseFixture implements OrderedFixtureInterface
{
    public const SUPER_ADMIN_USER_REFERENCE = 'super-admin-user';
    public const ADMIN_USER_REFERENCE = 'admin-user';
    public const SUPER_MODERATOR_USER_REFERENCE = 'super-moderator-user';
    public const MODERATOR_USER_REFERENCE = 'moderator-user';
    public const USER_USER_REFERENCE = 'user-user';

	public function __construct(
        private readonly MessageBusInterface  $messageBus,
        private readonly UserManagerInterface $userManager,
		private readonly SluggerInterface     $slugger
	) {
    }

    public function loadData(): void
    {
	    $rootUsername = 'root';
	    $rootEmail = 'root@ria.az';
	    $rootPassword = 'secret';

        $users = [
            1 => [
                'rootUsername' => 'root',
                'rootEmail' => 'root@ria.az',
                'rootPassword' => 'secret',
            ],
            2 => [
                'rootUsername' => 'user',
                'rootEmail' => 'user@local.dev',
                'rootPassword' => '123456',
            ],
            3 => [
                'rootUsername' => 'moderator',
                'rootEmail' => 'moderator@local.dev',
                'rootPassword' => '123456',
            ],
            4 => [
                'rootUsername' => 'admin',
                'rootEmail' => 'admin@local.dev',
                'rootPassword' => '123456',
            ],
        ];

        foreach ($users as $key => $user) {
            $newUser = $this->userManager
                ->findUserByEmail($user['rootEmail']);

            if (!$newUser) {
                $newUser = $this->userManager->create();
                $newUser->setUsername($user['rootUsername']);
                $newUser->setEmail($user['rootEmail']);
                $newUser->setPlainPassword($user['rootPassword']);
                $this->userManager->updateCanonicalFields($newUser);
                $this->userManager->updatePassword($newUser);

                $this->userManager->update($newUser);
            }

            switch ($key) {
                case 1:
                    $this->addReference(self::SUPER_ADMIN_USER_REFERENCE, $newUser);
                    break;
                case 2:
                    $this->addReference(self::USER_USER_REFERENCE, $newUser);
                    break;
                case 3:
                    $this->addReference(self::MODERATOR_USER_REFERENCE, $newUser);
                    break;
                case 4:
                    $this->addReference(self::ADMIN_USER_REFERENCE, $newUser);
                    break;
            }
//            $this->addReference(self::ADMIN_USER_REFERENCE, $newUser);
        }


        for ($i = 0; $i < 10; ++$i) {
			if ($i === 1) {
				$userAdmin = $this->userManager
					->findUserByEmail($rootEmail);

				if (!$userAdmin) {
					$userAdmin = $this->userManager->create();
					$userAdmin->setUsername($rootUsername);
					$userAdmin->setEmail($rootEmail);
					$userAdmin->setPlainPassword($rootPassword);
					$this->userManager->updateCanonicalFields($userAdmin);
					$this->userManager->updatePassword($userAdmin);

					$this->userManager->update($userAdmin);
				}

				$this->addReference(self::ADMIN_USER_REFERENCE.$i, $userAdmin);
			} else {
				$this->messageBus->dispatch(new CreateUserCommand(new CreateUserDto(
					'user'.$i,
					'user'.$i.'@test.com',
					'user'.$i,
					$i <= 3 || $this->faker->boolean,
					$this->faker->firstName,
					$this->faker->lastName,
				)));
			}

        }

        $this->addReferences($this->userManager);
        $this->userManager->flush();
    }

    public function getOrder(): int
    {
        return 1;
    }
}
