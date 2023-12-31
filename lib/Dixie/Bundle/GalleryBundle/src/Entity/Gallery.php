<?php

declare(strict_types=1);

namespace Talav\GalleryBundle\Entity;

use Talav\Component\Resource\Model\ResourceInterface;
use Talav\Component\User\Model\UserInterface;
use Talav\GalleryBundle\Repository\GalleryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Gallery.
 */
#[ORM\Entity(repositoryClass: GalleryRepository::class)]
#[ORM\Table(name: 'gallery')]
class Gallery implements ResourceInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: Types::INTEGER, options: ['unsigned' => true])]
    protected ?int $id;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank]
    protected string $name;

    #[ORM\ManyToOne(targetEntity: UserInterface::class)]
    #[ORM\JoinColumn(nullable: false)]
    protected UserInterface $user;

    /**
     * @var Collection<GalleryImage>
     */
    #[ORM\OneToMany(mappedBy: 'gallery', targetEntity: GalleryImage::class, cascade: ['remove'])]
    protected Collection $images;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->images = new ArrayCollection();
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
     * Getter for name.
     *
     * @return string Name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Setter for name.
     *
     * @param string $name Name
     *
     * @return $this Gallery entity
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Getter for user.
     *
     * @return UserInterface User entity
     */
    public function getUser(): UserInterface
    {
        return $this->user;
    }

    /**
     * Setter for user.
     *
     * @param UserInterface $user User entity
     *
     * @return $this Gallery entity
     */
    public function setUser(UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Getter for images.
     *
     * @return Collection<GalleryImage> Images collection
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    /**
     * Add image to collection.
     *
     * @param GalleryImage $image Image entity
     *
     * @return $this Gallery entity
     */
    public function addImage(GalleryImage $image): self
    {
        $this->images->add($image);

        return $this;
    }
}
