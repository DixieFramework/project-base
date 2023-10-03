<?php

declare(strict_types=1);

namespace Talav\PostBundle\Form\Type;

use Talav\Component\User\Repository\UserRepositoryInterface;
use Talav\CoreBundle\Form\Type\SimpleEditorType;
use Talav\PostBundle\Entity\PostInterface;
use Talav\PostBundle\Entity\Song;
use Talav\PostBundle\Entity\Post;
use Talav\PostBundle\Entity\Tag;
use Talav\PostBundle\Entity\User;
use Talav\PostBundle\Repository\FollowRepository;
use Talav\PostBundle\Repository\PlaylistSongRepository;
use Talav\PostBundle\Repository\SongRepository;
use Talav\PostBundle\Repository\TagRepository;
use Talav\PostBundle\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Contracts\Translation\TranslatorInterface;
use Talav\UserBundle\Model\UserInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;

class PostType extends AbstractType
{
    private $translator;
    private $songs;
    private $user;
    private $role;
    private $tags;
    private $playlistSongs;
    private $follows;
    private $users;

    public function __construct(Security $security, AuthorizationCheckerInterface $authorizationChecker, TranslatorInterface $translator, TagRepository $tags, UserRepositoryInterface $userRepository)
    {
        $this->user = $security->getUser();
        $this->role = $authorizationChecker;
        $this->translator = $translator;
        $this->tags = $tags;
        $this->users = $userRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        $builder
//            ->add('tags', EntityType::class, [
//                'label' => 'categories',
//                'help' => 'post.tags.help',
//                'class' => Tag::class,
//                'multiple' => true,
//                'required' => false,
//                'choice_label' => 'title',
//                'choices' => $this->tags->findBy(['type' => 'post']),
//                'label_attr' => ['class' => 'checkbox-custom'],
//                'attr' => [
//                    'data-placeholder' => $this->translator->trans('select.categories'),
//                    'class' => 'chosen'
//                ]
//            ])
//        ;

        $post = $builder->getData();
        $userFollowing = null;//$this->follows->findOneBy(['follower' => $this->user]);

        if ($post->isNew() || $post->getAuthor() == $this->user) {
//        if (!$post->getId() || $post->getAuthor() == $this->user) {
            $builder
//                ->add('title', TextType::class, [
//                    'label' => 'heading',
//                    'help' => 'post.title.help',
//                    'required' => false,
//                    'constraints' => [
//                        new Length([
//                            'max' => 80,
//                            'maxMessage' => 'max.message'
//                        ])
//                    ]
//                ])
                ->add('content', SimpleEditorType::class, [
                    'label' => 'description',
                    'required' => true,
                    'attr' => [
                        'style' => 'opacity:0;margin-bottom:20px',
                        'class' => 'ckeditor',
                        'rows' => 10
                    ],
                    'constraints' => [
                        new Length([
                            'min' => 10,
                            'minMessage' => 'min.message',
                            'max' => 5000,
                            'maxMessage' => 'max.message'
                        ])
                    ]
                ])
            ;
        }

        if ($post->isNew() || !$post->isNew() && $post->getAuthor() == $this->user && $post->getPublishedAt()->getTimestamp() === $post->getUpdatedAt()->getTimestamp()) {
//        if (!$post->getId() || $post->getId() && $post->getAuthor() == $this->user && $post->getPublishedAt()->getTimestamp() === $post->getUpdatedAt()->getTimestamp()) {
            $builder
                ->add('imageFile', VichImageType::class, [
                    'label' => 'image',
                    'required' => false,
                    'download_uri' => false,
                    'image_uri' => false,
                    'allow_delete' => false
                ])
            ;
        }

        if ($post->isNew() && $userFollowing !== null || $post->getAuthor() == $this->user && $userFollowing !== null ) {
        //if (!$post->getId() && $userFollowing !== null || $post->getAuthor() == $this->user && $userFollowing !== null ) {
            $builder
                ->add('taggedUsers', EntityType::class, [
                    'label' => 'users',
                    'help' => 'tagged.users.help',
                    'class' => UserInterface::class,
                    'multiple' => true,
                    'required' => false,
                    'choice_label' => 'username',
                    'label_attr' => ['class' => 'checkbox-custom'],
                    'choices' => $this->users->findFollows(['user' => $this->user, 'type' => 'following']),
                    'attr' => [
                        'data-placeholder' => $this->translator->trans('select.users'),
                        'class' => 'chosen-users'
                    ]
                ])
            ;
        }

        if (!$post->isNew() && $post->getStatus() !== null && $this->role->isGranted('ROLE_POST_MODERATOR') && $post->getAuthor() !== $this->user) {
        //if ($post->getId() && $post->getStatus() !== null && $this->role->isGranted('ROLE_POST_MODERATOR') && $post->getAuthor() !== $this->user) {
            $builder
                ->add('moderation', CheckboxType::class, [
                    'label' => 'to.moderation',
                    'required' => false,
                    'mapped' => false,
                    'label_attr' => ['class' => 'switch-custom']
                ])
                ->add('gender', ChoiceType::class, [
                    'choices' => [
                        'content.for.all' => null,
                        'content.for.male.audience' => false,
                        'content.for.female.audience' => true
                    ],
                    'attr' => ['class' => 'chosen']
                ])
                ->add('featured', CheckboxType::class, [
                    'label' => 'featured',
                    'required' => false,
                    'label_attr' => ['class' => 'switch-custom']
                ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PostInterface::class
        ]);
    }
}
