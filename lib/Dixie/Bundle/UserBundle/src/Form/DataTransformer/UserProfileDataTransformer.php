<?php

declare(strict_types=1);

namespace Talav\UserBundle\Form\DataTransformer;

use Doctrine\ORM\EntityManagerInterface;
use Groshy\Entity\Profile;
use Symfony\Component\Form\DataTransformerInterface;
use Talav\ProfileBundle\Entity\ProfileInterface;

class UserProfileDataTransformer implements DataTransformerInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected $doctrine;

    /**
     * @param EntityManagerInterface $doctrine
     */
    public function __construct(EntityManagerInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @param ProfileInterface $profile
     *
     * @return array|ProfileInterface
     */
    public function transform($profile)
    {
        if ($profile instanceof ProfileInterface) {
            $user = $profile->getUser();
            $array = [
            'id'            => $profile->getId(),
            'user'          => $profile->getUser()->getId(),
            'firstName'     => $profile->getFirstName(),
            'lastName'      => $profile->getLastName(),
          ];

            return $array;
        }

        return $profile;
    }

    /**
     * @param string $value
     *
     * @return UserProfile
     */
    public function reverseTransform($value)
    {
        $profile = new Profile();

        $profileRepository = $this->doctrine->getRepository(Profile::class);

        if (isset($value['id'])) {
            $profile = $profileRepository->find($value['id']);
        }
        if (isset($value['firstName'])) {
            $profile->setFirstName($value['firstName']);
        }
        if (isset($value['lastName'])) {
            $profile->setLastName($value['lastName']);
        }

        return $profile;
    }
}
