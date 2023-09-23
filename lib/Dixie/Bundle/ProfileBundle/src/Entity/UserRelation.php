<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Entity;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Validator\Constraints;
use Talav\Component\Resource\Model\ResourceTrait;
use Talav\Component\User\Model\UserInterface;
use Talav\ProfileBundle\Repository\UserRelationRepository;

/**
 * Specialrelations.
 *
 *
 * @SuppressWarnings(PHPMD)
 * Auto generated class do not check mess
 */
#[ORM\Table(name: 'user_relation')]
#[ORM\Index(name: 'owner_id', columns: ['owner_id'])]
#[ORM\UniqueConstraint(name: 'unique_relation', columns: ['owner_id', 'relation_id'])]
#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: UserRelationRepository::class)]
class UserRelation
{
    use ResourceTrait;

    #[ORM\Column(name: 'comment', type: Types::INTEGER, nullable: false)]
    protected int $comment = 0;

    #[ORM\Column(type: 'text')]
    #[Constraints\NotBlank]
    protected string $commentText = "";

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_IMMUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    protected ?\DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_IMMUTABLE,  options: ['default' => 'CURRENT_TIMESTAMP'])]
    protected ?\DateTimeImmutable $updatedAt;

    #[ORM\ManyToOne(targetEntity: UserInterface::class)]
    #[ORM\JoinColumn(name: 'owner_id', referencedColumnName: 'id', nullable: false)]
    protected UserInterface $owner;

    #[ORM\ManyToOne(targetEntity: UserInterface::class)]
    #[ORM\JoinColumn(name: 'relation_id', referencedColumnName: 'id', nullable: false)]
    protected UserInterface $receiver;

    #[ORM\Column(name: 'confirmed', type: Types::STRING, nullable: false)]
    protected string $confirmed = 'No';

    public function setComment(int $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getComment(): int
    {
        return $this->comment;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function setOwner(UserInterface $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner.
     *
     * @return UserInterface
     */
    public function getOwner(): UserInterface
    {
        return $this->owner;
    }

    public function setReceiver(UserInterface $receiver): self
    {
        $this->receiver = $receiver;

        return $this;
    }

    public function getReceiver(): UserInterface
    {
        return $this->receiver;
    }

    public function setConfirmed(string $confirmed): self
    {
        $this->confirmed = $confirmed;

        return $this;
    }

    public function getConfirmed(): string
    {
        return $this->confirmed;
    }

    /**
     * Triggered after load from database.
     */
    #[ORM\PostLoad]
    public function onPostLoad(PostLoadEventArgs $args): void
    {
        $objectManager = $args->getObjectManager();
        $memberTranslationRepository = $objectManager->getRepository(MemberTranslation::class);
        $translatedComment = $memberTranslationRepository->findOneBy([
            'translation' => $this->comment,
            'owner' => $this->owner,
        ]);

        if ($translatedComment instanceof \Talav\ProfileBundle\Entity\MemberTranslation) {
            $this->commentText = $translatedComment->getSentence();
        }
    }

    /**
     * Triggered on insert.
     */
    #[ORM\PrePersist]
    public function onPrePersist(PrePersistEventArgs $args)
    {
        $this->createdAt = new \DateTimeImmutable('now');
        $this->updatedAt = $this->createdAt;

        $this->createRelationComment($args->getObjectManager());
    }

    /**
     * Triggered on update.
     */
    #[ORM\PostUpdate]
    public function onPostUpdate(PostUpdateEventArgs $args)
    {
        $objectManager = $args->getObjectManager();

        $languageRepository = $objectManager->getRepository(Language::class);
        $language = $languageRepository->findOneBy(['shortCode' => 'en']);
        $memberTranslationRepository = $objectManager->getRepository(MemberTranslation::class);
        $translatedComment = $memberTranslationRepository->findOneBy([
            'translation' => $this->comment,
            'owner' => $this->getOwner()
        ]);

        $translatedComment->setSentence($this->commentText);
        $objectManager->persist($translatedComment);
        $objectManager->flush();
    }

    private function createRelationComment(ObjectManager $objectManager)
    {
        $memberTranslationRepository = $objectManager->getRepository(MemberTranslation::class);
        $translatedComment = $memberTranslationRepository->findOneBy([
            'translation' => $this->comment,
            'owner' => $this->getOwner()
        ]);

        if (!$translatedComment instanceof \Talav\ProfileBundle\Entity\MemberTranslation) {
            $languageRepository = $objectManager->getRepository(Language::class);
            $language = $languageRepository->findOneBy(['shortCode' => 'en']);

            $translatedComment = new MemberTranslation();
            $translatedComment->setSentence($this->commentText);
            $translatedComment->setOwner($this->getOwner());
            $translatedComment->setTranslator($this->getOwner());
            $translatedComment->setLanguage($language);
            $translatedComment->setTablecolumn('specialrelations.comment');
            $objectManager->persist($translatedComment);
            $objectManager->flush();

            //Set translation ID to own id
            $translatedComment->setTranslation($translatedComment->getId());
            $objectManager->persist($translatedComment);
            $this->setComment($translatedComment->getId());
            $objectManager->flush();
        }
    }

    public function getCommentText(): string
    {
        return $this->commentText;
    }

    public function setCommentText(string $commentText): self
    {
        $this->commentText = $commentText;

        return $this;
    }
}
