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
	public const ADMIN_USER_REFERENCE = 'admin-user';

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

				$this->addReference(self::ADMIN_USER_REFERENCE, $userAdmin);
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
