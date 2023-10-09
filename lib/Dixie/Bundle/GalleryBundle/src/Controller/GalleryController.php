<?php

declare(strict_types=1);

namespace Talav\GalleryBundle\Controller;

use phpDocumentor\Reflection\Types\This;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Talav\Component\Resource\Manager\ManagerInterface;
use Talav\Component\User\Model\UserInterface;
use Talav\CoreBundle\Controller\AbstractController;
use Talav\CoreBundle\Interfaces\RoleInterface;
use Talav\GalleryBundle\Entity\Gallery;
use Talav\GalleryBundle\Entity\GalleryImage;
use Talav\GalleryBundle\Form\Type\GalleryType;
use Talav\GalleryBundle\Repository\GalleryRepository;
use Talav\GalleryBundle\Repository\GalleryImageRepository;
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
#[AsController]
#[Route('/gallery', name: 'talav_gallery_')]
#[IsGranted(RoleInterface::ROLE_USER)]
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

    private GalleryRepository $galleryRepository;

    private GalleryImageRepository $imageRepository;

    /**
     * Constructor.
     *
     * @param ManagerInterface $galleryManager
     * @param ManagerInterface $galleryImageManager
     * @param TranslatorInterface $translator
     */
    public function __construct(private readonly ManagerInterface $galleryManager, private readonly ManagerInterface $galleryImageManager, private readonly TranslatorInterface $translator)
    {
        $this->galleryRepository = $this->galleryManager->getRepository();
        $this->imageRepository = $this->galleryImageManager->getRepository();
    }

    /**
     * Index action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route(name: 'index', methods: 'GET')]
    public function index(Request $request, ManagerInterface $galleryManager): Response
    {
        $this->denyAccessUnlessGranted(GalleryVoter::LIST);

        /** @var UserInterface $user */
        $user = $this->getUser();
        $page = $request->query->getInt('page', 1);
        $galleries = $this->createQueryBuilderPaginator(
            $galleryManager->getRepository()->findAllByUserQueryBuilder($user),
            $page,
            self::GALLERY_PER_PAGE
        );

        foreach ($galleries as $gallery) {
            $gallery->coverImage = $this->entityManager->getRepository(GalleryImage::class)->findLastFromGallery($gallery);
        }


        return $this->render('@TalavGallery/gallery/index.html.twig', [
            'galleries' => $galleries,
        ]);



        $page = $request->query->getInt('page', 1);

        return $this->render('galleries/index.html.twig', [
            'pagination' => $this->galleryRepository->getPaginatedList($page)
        ]);
    }

    /**
     * View action.
     *
     * @param Request $request HTTP request
     * @param Gallery $gallery Gallery entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}', name: 'preview', requirements: ['id' => '[1-9]\d*'], methods: 'GET')]
    public function view(Request $request, Gallery $gallery): Response
    {
        $this->denyAccessUnlessGranted(GalleryVoter::VIEW, $gallery);

        /** @var UserInterface $user */
        $user = $this->getUser();

        $page = (int) $request->query->get('page', 1);
        $galleries = $this->createQueryBuilderPaginator(
            $this->imageRepository->findAllByGalleryQueryBuilder($gallery),
            $page,
            self::PHOTO_PER_PAGE
        );

//        $page = $request->query->getInt('page', 1);
//        $imagesPagination = $this->imageService->getPaginatedList($gallery, $page);

        return $this->render('@TalavGallery/gallery/preview.html.twig', [
            'gallery' => $gallery,
            'galleryImages' => $galleries,
            'imagesPagination' => $galleries
        ]);
    }

    /**
     * Edit action.
     *
     * @param Request $request HTTP request
     * @param Gallery $gallery Gallery entity
     *
     * @return Response HTTP response
     */
    #[Route('/edit/{id}', name: 'edit', requirements: ['id' => '[1-9]\d*'], methods: 'GET|POST')]
    public function edit(Request $request, Gallery $gallery): Response
    {
        $this->denyAccessUnlessGranted(GalleryVoter::EDIT, $gallery);

        $form = $this->createForm(GalleryType::class, $gallery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->galleryRepository->save($gallery, true);

            $this->successTrans('message.updated_successfully');

            return $this->redirectToRoute('talav_gallery_index');
        }

        return $this->render('@TalavGallery/gallery/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route('/create', name: 'create', methods: 'GET|POST')]
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

            $this->successTrans('message.created_successfully');

            return $this->redirectToRoute('talav_gallery_preview', ['id' => $gallery->getId()]);
        }

        return $this->render('@TalavGallery/gallery/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param Gallery $gallery Category entity
     *
     * @return Response HTTP response
     */
    #[Route('/delete/{id}', name: 'delete', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
    public function delete(Request $request, Gallery $gallery): Response
    {
        $this->denyAccessUnlessGranted(GalleryVoter::DELETE, $gallery);

        $form = $this->createForm(FormType::class, $gallery, [
            'method' => 'DELETE',
            'action' => $this->generateUrl('talav_gallery_delete', ['id' => $gallery->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->galleryRepository->delete($gallery);

            $this->successTrans('message.deleted_successfully');

            return $this->redirectToRoute('talav_gallery_index');
        }

        return $this->render('@TalavGallery/gallery/delete.html.twig', [
            'form' => $form->createView(),
            'gallery' => $gallery,
        ]);
    }
}
