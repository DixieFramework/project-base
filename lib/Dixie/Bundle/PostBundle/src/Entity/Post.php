<?php

declare(strict_types=1);

namespace Talav\PostBundle\Entity;

use Doctrine\Common\Collections\{ArrayCollection, Collection};
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Talav\Component\User\Model\UserInterface;
use Talav\PostBundle\Repository\PostRepository;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[Vich\Uploadable]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;
//
//    #[Vich\UploadableField(mapping: 'post_images', fileNameProperty: 'image')]
//    #[Assert\File(mimeTypes: ['image/jpeg', 'image/png', 'image/gif'], mimeTypesMessage: 'image.have.to.be.jpg.or.png')]
//    private $imageFile;
//
//    #[ORM\Column(type: 'string', length: 255, nullable: true)]
//    private $image;
//
//    #[ORM\Column(type: 'text', nullable: true)]
//    private $content;
//
//    #[ORM\Column(type: 'text', nullable: true)]
//    #[Assert\Length(max: '150', maxMessage: 'form.max.message')]
//    private $description;
//
//    #[ORM\Column(type: 'boolean', nullable: true)]
//    private $status;
//
//    #[ORM\Column(type: 'string', length: 255)]
//    private $slug;
//
//    #[ORM\Column(type: 'datetime', nullable: true)]
//    private $publishedAt;
//
//    #[ORM\Column(type: 'datetime', nullable: true)]
//    #[Assert\Type(\DateTimeInterface::class)]
//    private $updatedAt;
//
//    #[ORM\ManyToOne(targetEntity: UserInterface::class, inversedBy: 'posts')]
//    #[ORM\JoinColumn(nullable: false)]
//    private $author;
//
//    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Comment::class, orphanRemoval: true)]
//    private $comments;
//
//    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Bookmark::class, orphanRemoval: true)]
//    private $bookmarks;
//
//    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Notification::class, orphanRemoval: true)]
//    private $notifications;
//
//    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'posts')]
//    private $tags;
//
//    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Action::class, orphanRemoval: true)]
//    private $actions;
//
//    #[ORM\ManyToMany(targetEntity: UserInterface::class, inversedBy: 'taggedPosts')]
//    private $taggedUsers;
//
//    #[ORM\Column(type: 'boolean', nullable: true)]
//    private $featured;
//
//    #[ORM\Column(type: 'boolean', nullable: true)]
//    private $gender;
//
//    #[ORM\Column(type: 'string', length: 255, nullable: true)]
//    private $title = null;
//
//    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Like::class, orphanRemoval: true)]
//    private $likes;
//
//    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Message::class)]
//    private $messages;
//
//    #[Pure] public function __construct()
//    {
//        $this->songs = new ArrayCollection();
//        $this->comments = new ArrayCollection();
//        $this->bookmarks = new ArrayCollection();
//        $this->notifications = new ArrayCollection();
//        $this->tags = new ArrayCollection();
//        $this->actions = new ArrayCollection();
//        $this->taggedUsers = new ArrayCollection();
//        $this->likes = new ArrayCollection();
//        $this->messages = new ArrayCollection();
//    }

}
