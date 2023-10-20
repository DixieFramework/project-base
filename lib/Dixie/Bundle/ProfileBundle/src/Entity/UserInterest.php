<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToOne;
use Talav\ProfileBundle\Repository\UserInterestRepository;

#[ORM\Entity(repositoryClass: UserInterestRepository::class)]
#[ORM\Table('user_interest')]
class UserInterest
{
	#[ORM\Id]
	#[ORM\OneToOne(targetEntity: Interest::class)]
	#[ORM\JoinColumn(name: 'interest_id', referencedColumnName: 'id')]
	protected Interest $interest;

	#[ORM\Id]
	#[ORM\OneToOne(targetEntity: ProfileInterface::class)]
	#[ORM\JoinColumn(name: 'profile_id', referencedColumnName: 'id')]
	protected ProfileInterface $profile;

    public function getInterest(): Interest
    {
        return $this->interest;
    }

    public function setInterest(Interest $interest): void
    {
        $this->interest = $interest;
    }

    public function getProfile(): ProfileInterface
    {
        return $this->profile;
    }

    public function setProfile(ProfileInterface $profile): void
    {
        $this->profile = $profile;
    }
}
