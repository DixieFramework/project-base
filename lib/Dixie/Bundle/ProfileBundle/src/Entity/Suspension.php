<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Talav\Component\Resource\Model\ResourceInterface;
use Talav\Component\Resource\Model\ResourceTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Talav\Component\Resource\Model\TimestampableTrait;
use Talav\Component\User\Model\UserInterface;
use Talav\ProfileBundle\Entity\ProfileInterface;

//#[ORM\Table(name: 'suspension')]
//#[ORM\Entity(repositoryClass: SuspensionRepository::class)]
//#[ORM\HasLifecycleCallbacks]
class Suspension implements ResourceInterface
{
	use TimestampableTrait;
    use ResourceTrait;

	public const REASON_DEFAULT = 'Inappropriate behavior';

    #[ORM\ManyToOne(targetEntity: ProfileInterface::class, inversedBy: 'suspensions')]
    #[ORM\JoinColumn(nullable: false)]
    protected ?ProfileInterface $profile = null;

	#[ORM\ManyToOne(targetEntity: UserInterface::class)]
	#[ORM\JoinColumn(name: 'user_opened_id', referencedColumnName: 'id')]
	protected ?UserInterface $userOpened = null;

	#[ORM\ManyToOne(targetEntity: UserInterface::class)]
	#[ORM\JoinColumn(name: 'user_closed_id', referencedColumnName: 'id')]
	protected ?UserInterface $userClosed = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: false)]
    #[Assert\GreaterThan('today UTC', message: 'user.suspension.suspended_until.past')]
    protected ?DateTimeInterface $suspendedUntil = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected ?string $reason = null;

//	#[ORM\Column(name: 'created_at', type: Types::DATETIMETZ_IMMUTABLE)]
//    protected ?\DateTimeInterface $createdAt = null;
//
//	#[ORM\Column(name: 'updated_at', type: Types::DATETIMETZ_IMMUTABLE)]
//    protected ?\DateTimeInterface $updatedAt = null;

	#[ORM\Column(name: 'reasons', type: 'json')]
	protected ?array $reasons = null;

    public static function create(ProfileInterface $user, DateTimeInterface $until, ?string $reason = null): Suspension
    {
        $suspension = new self();
        $suspension->setProfile($user);
        $suspension->setSuspendedUntil($until);
        $suspension->setReason($reason);

        $user->addSuspension($suspension);

        return $suspension;
    }

	#[ORM\PrePersist]
	public function prePersist(): void
	{
        $this->setCreatedAtWithCurrentTime();
//		$this->createdAt = new DateTime('UTC');
//		$this->updatedAt = new DateTime('UTC');
	}

	#[ORM\PreUpdate]
	public function preUpdate(): void
	{
		$this->setUpdatedAtWithCurrentTime();
	}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProfile(): ?ProfileInterface
    {
        return $this->profile;
    }

    public function setProfile(?ProfileInterface $profile): self
    {
        $this->profile = $profile;

        return $this;
    }

	public function getUserOpened(): UserInterface
	{
		return $this->userOpened;
	}

	public function setUserOpened(UserInterface $moderator): void
	{
		$this->userOpened = $moderator;
	}

	public function getUserClosed(): UserInterface
	{
		return $this->userClosed;
	}

	public function setUserClosed(UserInterface $userClosed): void
	{
		$this->userClosed = $userClosed;
	}

    public function getSuspendedUntil(): ?DateTimeInterface
    {
        return $this->suspendedUntil;
    }

    public function setSuspendedUntil(?DateTimeInterface $suspendedUntil): self
    {
        $this->suspendedUntil = match (true) {
            null !== $suspendedUntil => \DateTimeImmutable::createFromInterface($suspendedUntil),
            default => null
        };

        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(?string $reason): self
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getReasons(): array
    {
        $roles = $this->reasons;
        // we need to make sure to have at least one role
        $roles[] = static::REASON_DEFAULT;
        return array_values(array_unique($roles));
    }

    public function setReasons(?array $reasons): self
    {
        $this->reasons = $reasons;

        return $this;
    }

    public function addReason($reason): self
    {
        $reason = strtoupper($reason);
        if (!in_array($reason, $this->reasons, true)) {
            $this->reasons[] = $reason;
        }

        return $this;
    }

    public function isActive(): bool
    {
    	return $this->createDateTime('now') < $this->getSuspendedUntil();
        //return self::getCurrentDateTime() < $this->getSuspendedUntil();
    }

    public function getDaysUntilLifted(): ?int
    {
	    $start = new DateTime('now');
	    $end = $this->getSuspendedUntil();

	    return (int) $end->diff($start)->format("%a");
    }
}
