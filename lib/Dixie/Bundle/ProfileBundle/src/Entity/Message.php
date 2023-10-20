<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Entity;

use Talav\Component\Resource\Model\ResourceTrait;
use Talav\PostBundle\Entity\PostInterface;
use Talav\ProfileBundle\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Talav\ProfileBundle\Entity\ProfileInterface;
use Talav\UserBundle\Model\UserInterface;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[ORM\Table('message')]
#[Vich\Uploadable]
class Message implements MessageInterface
{
    use ResourceTrait;

    #[ORM\ManyToOne(targetEntity: UserInterface::class, inversedBy: 'sentMessages')]
    #[ORM\JoinColumn(nullable: false)]
    protected ?UserInterface $sender;

    #[ORM\ManyToOne(targetEntity: UserInterface::class, inversedBy: 'receivedMessages')]
    #[ORM\JoinColumn(nullable: false)]
    protected ?UserInterface $receiver;

    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $content;

    #[ORM\Column(type: 'datetime')]
    protected ?\DateTimeInterface $sentAt;

    #[ORM\Column(type: 'boolean')]
    protected ?bool $seen;

    #[ORM\Column(type: 'boolean')]
    protected ?bool $sender_deleted;

    #[ORM\Column(type: 'boolean')]
    protected ?bool $receiver_deleted;

//    #[ORM\ManyToOne(targetEntity: Song::class, inversedBy: 'messages')]
//    protected ?Song $song;

    #[ORM\ManyToOne(targetEntity: PostInterface::class, inversedBy: 'messages')]
    protected ?PostInterface $post;

    #[ORM\ManyToOne(targetEntity: ProfileInterface::class, inversedBy: 'messages')]
    protected ?ProfileInterface $profile;

    #[ORM\ManyToOne(targetEntity: MessageInterface::class)]
    protected ?MessageInterface $replyTo;

    #[Vich\UploadableField(mapping: 'message_images', fileNameProperty: 'image')]
    #[Assert\File(mimeTypes: ['image/jpeg','image/png','image/gif'], mimeTypesMessage: 'image.have.to.be.jpg.or.png')]
    protected $imageFile;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected ?string $image;

    #[ORM\Column(type: 'datetime', nullable: true)]
    protected ?\DateTimeInterface $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSender(): ?UserInterface
    {
        return $this->sender;
    }

    public function setSender(?UserInterface $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function getReceiver(): ?UserInterface
    {
        return $this->receiver;
    }

    public function setReceiver(?UserInterface $receiver): self
    {
        $this->receiver = $receiver;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getSentAt(): ?\DateTimeInterface
    {
        return $this->sentAt;
    }

    public function setSentAt(\DateTimeInterface $sentAt): self
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    public function getSeen(): ?bool
    {
        return $this->seen;
    }

    public function setSeen(bool $seen): self
    {
        $this->seen = $seen;

        return $this;
    }

    public function getSenderDeleted(): ?bool
    {
        return $this->sender_deleted;
    }

    public function setSenderDeleted(bool $sender_deleted): self
    {
        $this->sender_deleted = $sender_deleted;

        return $this;
    }

    public function getReceiverDeleted(): ?bool
    {
        return $this->receiver_deleted;
    }

    public function setReceiverDeleted(bool $receiver_deleted): self
    {
        $this->receiver_deleted = $receiver_deleted;

        return $this;
    }

//    public function getSong(): ?Song
//    {
//        return $this->song;
//    }
//
//    public function setSong(?Song $song): self
//    {
//        $this->song = $song;
//
//        return $this;
//    }

    public function getPost(): ?PostInterface
    {
        return $this->post;
    }

    public function setPost(?PostInterface $post): self
    {
        $this->post = $post;

        return $this;
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

    public function getReplyTo(): ?MessageInterface
    {
        return $this->replyTo;
    }

    public function setReplyTo(?MessageInterface $replyTo): self
    {
        $this->replyTo = $replyTo;

        return $this;
    }

    /**
     * @param File|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if ($imageFile instanceof \Symfony\Component\HttpFoundation\File\File) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
