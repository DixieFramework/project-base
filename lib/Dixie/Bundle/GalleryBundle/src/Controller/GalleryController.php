<?php

declare(strict_types=1);

namespace Talav\GalleryBundle\Controller;

use Talav\Component\User\Model\UserInterface;
use Talav\CoreBundle\Controller\AbstractController;
use Talav\GalleryBundle\Entity\Gallery;
use Talav\GalleryBundle\Form\Type\GalleryType;
use Talav\GalleryBundle\Repository\GalleryRepository;
use Talav\GalleryBundle\Repository\ImageRepository;
use Talav\GalleryBundle\Voter\GalleryVoter;
use Talav\GalleryBundle\Service\GalleryServiceInterface;
use Talav\GalleryBundle\Service\ImageServiceInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class GalleriesController.
 */
#[Route('/gallery')]
class GalleryController extends AbstractController
{
    /**
     * @var int
     */
    private const GALLERY_PER_PAGE = 25;

    /**
     * @var int
     */
    private const PHOTO_PER_PAGE = 25;

    /**
     * Constructor.
     *
     * @param GalleryServiceInterface $galleryRepository Gallery service
     * @param ImageServiceInterface   $imageService   Image service
     * @param TranslatorInterface     $translator     Translator
     */
//    public function __construct(private GalleryServiceInterface $galleryService, private ImageServiceInterface $imageService, private TranslatorInterface $translator)
    public function __construct(private readonly GalleryRepository $galleryRepository, private readonly ImageRepository $imageService, private TranslatorInterface $translator)
    {
    }

    /**
     * Index action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route(name: 'gallery_index', methods: 'GET')]
    public function index(Request $request, GalleryRepository $galleryRepository): Response
    {
        $this->denyAccessUnlessGranted(GalleryVoter::LIST);

        /** @var UserInterface $user */
        $user = $this->getUser();
        $page = (int) $request->query->get('page', 1);
        $galleries = $this->createQueryBuilderPaginator(
            $galleryRepository->findAllByUserQueryBuilder($user),
            $page,
            self::GALLERY_PER_PAGE
        );

        return $this->render('@TalavGallery/gallery/index.html.twig', [
            'galleries' => $galleries->getCurrentPageResults(),
        ]);



        $page = $request->query->getInt('page', 1);

        return $this->render(
            'galleries/index.html.twig',
            ['pagination' => $this->galleryRepository->getPaginatedList($page)],
        );
    }

    /**
     * View action.
     *
     * @param Request $request HTTP request
     * @param Gallery $gallery Gallery entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}',
        name: 'gallery_preview',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET',
    )]
    public function view(Request $request, Gallery $gallery): Response
    {
        $this->denyAccessUnlessGranted(GalleryVoter::VIEW, $gallery);

        $page = (int) $request->query->get('page', 1);
        $galleries = $this->createQueryBuilderPaginator(
            $this->galleryRepository->findAllByUserQueryBuilder($user),
            $page,
            self::PHOTO_PER_PAGE
        );




        $page = $request->query->getInt('page', 1);
        $imagesPagination = $this->imageService->getPaginatedList($gallery, $page);

        return $this->render(
            '@TalavGallery/gallery/preview.html.twig',
            ['gallery' => $gallery, 'imagesPagination' => $imagesPagination],
        );
    }

    /**
     * Edit action.
     *
     * @param Request $request HTTP request
     * @param Gallery $gallery Gallery entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/edit/{id}',
        name: 'gallery_edit',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET|POST',
    )]
    public function edit(Request $request, Gallery $gallery): Response
    {
        $this->denyAccessUnlessGranted(GalleryVoter::EDIT, $gallery);

        $form = $this->createForm(GalleryType::class, $gallery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->galleryRepository->save($gallery, true);

            $this->addFlash(
                'success',
                $this->translator->trans('message.updated_successfully')
            );

            return $this->redirectToRoute('gallery_index');
        }

        return $this->render(
            '@TalavGallery/gallery/edit.html.twig',
            ['form' => $form->createView()],
        );
    }

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route(
        '/create',
        name: 'gallery_create',
        methods: 'GET|POST',
    )]
    public function create(Request $request): Response
    {
        /** @var ?UserInterface $user */
        $user = $this->getUser();
//        if (null === $user || false === $user->isSuperAdmin()) {
//            throw new HttpException(403);
//        }
        $gallery = new Gallery();
        $gallery->setUser($user);
        $form = $this->createForm(GalleryType::class, $gallery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($gallery);
            $this->entityManager->flush();

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('gallery_preview', ['id' => $gallery->getId()]);
        }

        return $this->render(
            '@TalavGallery/gallery/create.html.twig',
            ['form' => $form->createView()],
        );
    }

    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param Gallery $gallery Category entity
     *
     * @return Response HTTP response
     */
    #[Route('/delete/{id}', name: 'gallery_delete', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
    public function delete(Request $request, Gallery $gallery): Response
    {
        $this->denyAccessUnlessGranted(GalleryVoter::DELETE, $gallery);

        $form = $this->createForm(FormType::class, $gallery, [
            'method' => 'DELETE',
            'action' => $this->generateUrl('gallery_delete', ['id' => $gallery->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->galleryRepository->delete($gallery);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('gallery_index');
        }

        return $this->render(
            '@TalavGallery/gallery/delete.html.twig',
            [
                'form' => $form->createView(),
                'gallery' => $gallery,
            ]
        );
    }
}
