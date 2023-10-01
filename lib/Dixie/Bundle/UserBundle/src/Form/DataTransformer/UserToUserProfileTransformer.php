<?php

declare(strict_types=1);

namespace Talav\UserBundle\Form\DataTransformer;

use App\Entity\User;
use App\Form\Model\UserProfileModel;
use App\Repository\UserRepository;
use Symfony\Component\Form\DataTransformerInterface;

class UserToUserProfileTransformer implements DataTransformerInterface
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    /**
     * @param User $user
     */
    public function transform($user): UserProfileModel
    {
        if (!$user instanceof User) {
            throw new \LogicException('The UserProfileType can only be used with User objects');
        }

        $userProfileModel = new UserProfileModel();
        $userProfileModel->setUsername($user->getUsername());
        $userProfileModel->setCurrentPassword($user->getPassword());
        $userProfileModel->setMailAddress($user->getMailAddress());
        $userProfileModel->setUserInfos($user->getUserInfos());
        return $userProfileModel;
    }

    /**
     *  @param UserProfileModel $userProfileModel
     */
    public function reverseTransform($userProfileModel): User
    {
        $user = $this->userRepository->findOneBy(['username' => $userProfileModel->getUsername()]);

        if ($userProfileModel->getNewPassword()) {
            $user->setPlainPassword($userProfileModel->getNewPassword());
        }

        if ($userProfileModel->getMailAddress()) {
            $user->setMailAddress($userProfileModel->getMailAddress());
        }

        return $user;
    }
}
