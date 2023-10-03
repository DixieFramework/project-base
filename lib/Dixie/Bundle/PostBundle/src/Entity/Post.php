<?php

declare(strict_types=1);

namespace Talav\PostBundle\Entity;

use Doctrine\Common\Collections\{ArrayCollection, Collection};
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Talav\Component\Resource\Model\ResourceInterface;
use Talav\Component\Resource\Model\ResourceTrait;
use Talav\Component\User\Model\UserInterface;
use Talav\CoreBundle\Utils\StringUtils;
use Talav\PostBundle\Repository\PostRepository;
use Talav\ProfileBundle\Entity\Like;
use Talav\ProfileBundle\Entity\LikeInterface;
use Talav\ProfileBundle\Entity\MessageInterface;
use Talav\ProfileBundle\Entity\NotificationInterface;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

//#[ORM\Entity(repositoryClass: PostRepository::class)]
//#[ORM\Table('post')]
#[ORM\MappedSuperclass]
#[Vich\Uploadable]
abstract class Post implements PostInterface
{
	use ResourceTrait;

    #[Vich\UploadableField(mapping: 'post_images', fileNameProperty: 'image')]
    #[Assert\File(mimeTypes: ['image/jpeg', 'image/png', 'image/gif'], mimeTypesMessage: 'image.have.to.be.jpg.or.png')]
    protected $imageFile;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected $image;

    #[ORM\Column(type: 'text', nullable: true)]
    protected $content;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\Length(max: 150, maxMessage: 'form.max.message')]
    protected $description;

    #[ORM\Column(type: 'boolean', nullable: true)]
    protected $status;

    #[ORM\Column(type: 'string', length: 255)]
//    #[Gedmo\Slug(fields: ['title', 'code'])]
    protected $slug;

    #[ORM\Column(type: 'datetime', nullable: true)]
    protected $publishedAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Assert\Type(\DateTimeInterface::class)]
    protected $updatedAt;

    #[ORM\ManyToOne(targetEntity: UserInterface::class, inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    protected $author;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Comment::class, orphanRemoval: true)]
    protected Collection $comments;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Bookmark::class, orphanRemoval: true)]
    protected Collection $bookmarks;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: NotificationInterface::class, orphanRemoval: true)]
    protected Collection $notifications;

    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'posts')]
    protected Collection $tags;

//    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Action::class, orphanRemoval: true)]
//    protected $actions;

    #[ORM\ManyToMany(targetEntity: UserInterface::class, inversedBy: 'taggedPosts')]
    protected $taggedUsers;

    #[ORM\Column(type: 'boolean', nullable: true)]
    protected $featured;

    #[ORM\Column(type: 'boolean', nullable: true)]
    protected $gender;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected ?string $title = null;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: LikeInterface::class, orphanRemoval: true)]
    protected Collection $likes;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: MessageInterface::class)]
    protected Collection $messages;

    #[Pure] public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->bookmarks = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->actions = new ArrayCollection();
        $this->taggedUsers = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }

	/**
	 * @param File|null $imageFile
	 */
	public function setImageFile(?File $imageFile = null): void
	{
		$this->imageFile = $imageFile;

		if (null !== $imageFile) {
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

	public function getContent(): ?string
	{
		return $this->content;
	}

	public function setContent(?string $content): self
	{
		$this->content = $content;

		return $this;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

	public function setDescription(?string $description): self
	{
		$this->description = $description;

		return $this;
	}

	public function getStatus(): ?bool
	{
		return $this->status;
	}

	public function setStatus(?bool $status): self
	{
		$this->status = $status;

		return $this;
	}

	public function getSlug(): ?string
	{
		return $this->slug;
	}

	public function setSlug(string $slug): self
	{
		$this->slug = $slug;

		return $this;
	}

	public function getPublishedAt(): ?\DateTimeInterface
	{
		return $this->publishedAt;
	}

	public function setPublishedAt(\DateTimeInterface $publishedAt): self
	{
		$this->publishedAt = $publishedAt;

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

	/**
	 * @return Collection
	 */
	public function getSongs(): Collection
	{
		return $this->songs;
	}

	public function addSong(Song $song): self
	{
		if (!$this->songs->contains($song)) {
			$this->songs[] = $song;
		}

		return $this;
	}

	public function removeSong(Song $song): self
	{
		if ($this->songs->contains($song)) {
			$this->songs->removeElement($song);
		}

		return $this;
	}

	public function getAuthor(): ?UserInterface
	{
		return $this->author;
	}

	public function setAuthor(?UserInterface $author): self
	{
		$this->author = $author;

		return $this;
	}

	/**
	 * @return Collection
	 */
	public function getComments(): Collection
	{
		return $this->comments;
	}

	public function addComment(Comment $comment): self
	{
		if (!$this->comments->contains($comment)) {
			$this->comments[] = $comment;
			$comment->setPost($this);
		}

		return $this;
	}

	public function removeComment(Comment $comment): self
	{
		if ($this->comments->contains($comment)) {
			$this->comments->removeElement($comment);
			if ($comment->getPost() === $this) {
				$comment->setPost(null);
			}
		}

		return $this;
	}

	/**
	 * @return Collection
	 */
	public function getBookmarks(): Collection
	{
		return $this->bookmarks;
	}

	public function addBookmark(Bookmark $bookmark): self
	{
		if (!$this->bookmarks->contains($bookmark)) {
			$this->bookmarks[] = $bookmark;
			$bookmark->setPost($this);
		}

		return $this;
	}

	public function removeBookmark(Bookmark $bookmark): self
	{
		if ($this->bookmarks->contains($bookmark)) {
			$this->bookmarks->removeElement($bookmark);
			if ($bookmark->getPost() === $this) {
				$bookmark->setPost(null);
			}
		}

		return $this;
	}

	/**
	 * @return Collection
	 */
	public function getNotifications(): Collection
	{
		return $this->notifications;
	}

	public function addNotification(NotificationInterface $notification): self
	{
		if (!$this->notifications->contains($notification)) {
			$this->notifications[] = $notification;
			$notification->setPost($this);
		}

		return $this;
	}

	public function removeNotification(NotificationInterface $notification): self
	{
		if ($this->notifications->contains($notification)) {
			$this->notifications->removeElement($notification);
			if ($notification->getPost() === $this) {
				$notification->setPost(null);
			}
		}

		return $this;
	}

	/**
	 * @return Collection
	 */
	public function getTags(): Collection
	{
		return $this->tags;
	}

	public function addTag(Tag $tag): self
	{
		if (!$this->tags->contains($tag)) {
			$this->tags[] = $tag;
		}

		return $this;
	}

	public function removeTag(Tag $tag): self
	{
		if ($this->tags->contains($tag)) {
			$this->tags->removeElement($tag);
		}

		return $this;
	}

	/**
	 * @return Collection
	 */
	public function getActions(): Collection
	{
		return $this->actions;
	}

	public function addAction(Action $action): self
	{
		if (!$this->actions->contains($action)) {
			$this->actions[] = $action;
			$action->setPost($this);
		}

		return $this;
	}

	public function removeAction(Action $action): self
	{
		if ($this->actions->contains($action)) {
			$this->actions->removeElement($action);
			if ($action->getPost() === $this) {
				$action->setPost(null);
			}
		}

		return $this;
	}

	/**
	 * @return Collection
	 */
	public function getTaggedUsers(): Collection
	{
		return $this->taggedUsers;
	}

	public function addTaggedUser(UserInterface $taggedUser): self
	{
		if (!$this->taggedUsers->contains($taggedUser)) {
			$this->taggedUsers[] = $taggedUser;
		}

		return $this;
	}

	public function removeTaggedUser(UserInterface $taggedUser): self
	{
		if ($this->taggedUsers->contains($taggedUser)) {
			$this->taggedUsers->removeElement($taggedUser);
		}

		return $this;
	}

	public function getFeatured(): ?bool
	{
		return $this->featured;
	}

	public function setFeatured(bool $featured): self
	{
		$this->featured = $featured;

		return $this;
	}

	public function getGender(): ?bool
	{
		return $this->gender;
	}

	public function setGender(?bool $gender): self
	{
		$this->gender = $gender;

		return $this;
	}

	public function getTitle(): ?string
	{
		return $this->title;
	}

	public function setTitle(?string $title): self
	{
		$this->title = $title;

		return $this;
	}

	/**
	 * @return Collection
	 */
	public function getLikes(): Collection
	{
		return $this->likes;
	}

	public function addLike(LikeInterface $like): self
	{
		if (!$this->likes->contains($like)) {
			$this->likes[] = $like;
			$like->setPost($this);
		}

		return $this;
	}

	public function removeLike(LikeInterface $like): self
	{
		if ($this->likes->contains($like)) {
			$this->likes->removeElement($like);
			if ($like->getPost() === $this) {
				$like->setPost(null);
			}
		}

		return $this;
	}

	/**
	 * @return Collection
	 */
	public function getMessages(): Collection
	{
		return $this->messages;
	}

	public function addMessage(MessageInterface $message): self
	{
		if (!$this->messages->contains($message)) {
			$this->messages[] = $message;
			$message->setPost($this);
		}

		return $this;
	}

	public function removeMessage(MessageInterface $message): self
	{
		if ($this->messages->contains($message)) {
			$this->messages->removeElement($message);
			if ($message->getPost() === $this) {
				$message->setPost(null);
			}
		}

		return $this;
	}

    public function generateSlug(UserInterface $user): void
    {
        if ($this->getTitle()) {
            $this->setSlug(StringUtils::slug($this->title));
        } else {
            $random = sprintf('%s %d', $user->getId(), rand(1,147) . rand(784,1217) * rand(342,635) . chr(rand(97,122)));
            $this->setSlug(StringUtils::slug($random));
        }
    }
}
