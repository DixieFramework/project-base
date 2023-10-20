<?php

declare(strict_types=1);

namespace Talav\GalleryBundle\Entity;

use Talav\Component\Media\Model\MediaInterface;
use Talav\Component\Resource\Model\ResourceInterface;
use Talav\GalleryBundle\Repository\GalleryImageRepository;
use Talav\ImageBundle\Entity\Image;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Image entity.
 */
#[ORM\Entity(repositoryClass: GalleryImageRepository::class)]
#[ORM\Table(name: 'gallery_image')]
class GalleryImage implements ResourceInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    protected ?int $id;

    #[ORM\Column(type: Types::STRING)]
//    #[Assert\NotBlank]
    private string $title;

    #[ORM\Column(type: Types::STRING, nullable: true)]
//    #[Assert\NotBlank]
    private string $description;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    protected ?bool $visible = null;

	#[ORM\ManyToOne(targetEntity: Image::class, cascade: ['persist'])]
	#[ORM\JoinColumn(nullable: true)]
	public ?Image $image = null;

//	#[ORM\OneToOne(targetEntity: "Talav\Component\Media\Model\MediaInterface", cascade: ['persist'])]
//	#[ORM\JoinColumn(name: 'media_id')]
//	protected ?MediaInterface $image = null;

    #[ORM\ManyToOne(targetEntity: Gallery::class, inversedBy: 'images')]
    #[ORM\JoinColumn(nullable: false)]
    private Gallery $gallery;

    /**
     * @var Collection<Comment>
     */
    #[ORM\OneToMany(mappedBy: 'image', targetEntity: Comment::class, cascade: ['remove'])]
    private Collection $comments;

    #[ORM\Column(name: 'created_at', type: Types::DATETIMETZ_IMMUTABLE, columnDefinition: 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP')]
    protected ?\DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: Types::DATETIMETZ_IMMUTABLE, nullable: true, columnDefinition: 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')]
    protected ?\DateTimeImmutable $updatedAt;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    /**
     * Getter for Id.
     *
     * @return int|null Id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter for title.
     *
     * @return string Image title
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Setter for title.
     *
     * @param string $title Image title
     *
     * @return $this Image entity
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Getter for description.
     *
     * @return string Image description
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Setter for description.
     *
     * @param string $description Image description
     *
     * @return $this Image entity
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getVisible(): ?bool
    {
        return $this->visible;
    }

    /**
     * @param bool|null $visible
     */
    public function setVisible(?bool $visible): void
    {
        $this->visible = $visible;
    }

//	public function getImage(): ?MediaInterface
//	{
//		return $this->image;
//	}
//
//	public function setImage(?MediaInterface $image): void
//	{
//		$this->image = $image;
//	}

    /**
     * Getter for gallery.
     *
     * @return Gallery Gallery entity
     */
    public function getGallery(): Gallery
    {
        return $this->gallery;
    }

    /**
     * @param Gallery $gallery Gallery entity
     *
     * @return $this Image entity
     */
    public function setGallery(Gallery $gallery): self
    {
        $this->gallery = $gallery;

        return $this;
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
}
