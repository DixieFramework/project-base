<?php

declare(strict_types=1);

namespace Talav\GalleryBundle\Controller;

use Talav\Component\Resource\Manager\ManagerInterface;
use Talav\CoreBundle\Controller\AbstractController;
use Talav\GalleryBundle\Entity\Gallery;
use Talav\GalleryBundle\Entity\GalleryImage;
use Talav\GalleryBundle\Form\Model\SearchFormModel;
use Talav\GalleryBundle\Form\Type\GalleryImageType;
use Talav\GalleryBundle\Form\Type\SearchGalleryImageFormType;
use Talav\GalleryBundle\Voter\ImageVoter;
use Talav\GalleryBundle\Service\ImageServiceInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Talav\GalleryBundle\Repository\GalleryImageRepository;
use Talav\ProfileBundle\Repository\UserAttributeRepository;

/**
 * Class ImagesController.
 */
#[Route('/gallery/image', name: 'talav_gallery_image_')]
class GalleryImageController extends AbstractController
{
    private GalleryImageRepository $imageRepository;

    /**
     * @var int
     */
    private const COMMENTS_PER_PAGE = 25;

    /**
     * Constructor.
     *
     * @param ManagerInterface $galleryImageManager
     * @param TranslatorInterface $translator
     */
    public function __construct(private readonly ManagerInterface $galleryImageManager, private TranslatorInterface $translator)
    {
        $this->imageRepository = $galleryImageManager->getRepository();
    }

	#[Route('', name: 'index', methods: 'GET|POST')]
	public function indexAction(Request $request, ManagerInterface $profileManager, UserAttributeRepository $userAttributeRepository): Response
	{
        $attributes = $userAttributeRepository->findOneByUserAndCategory($profileManager->getRepository()->find(2), 'Sex');
        dd($attributes);


        $profileRepo = $profileManager->getRepository();

        dd($profileRepo->findByProfileInfoStartsWith('11'));

		$searchOptions = [];

		$searchFormRequest = new SearchFormModel();
		$searchFormRequest->overrideFromRequest($request);

		$formFactory = $this->container->get('form.factory');
		$search = $formFactory->createNamed('search', SearchGalleryImageFormType::class, $searchFormRequest, [
			'search_options' => $searchOptions,
		]);

		$search->handleRequest($request);
		if ($search->isSubmitted() && $search->isValid()) {
			$data = $search->getData();
dd($data);
		}

		return $this->render('@TalavGallery/image/index.html.twig', [
			'form' => $search->createView()
		]);

		return new Response('Hello world from talav_gallery');
	}

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     * @param Gallery $gallery Gallery entity
     *
     * @return Response HTTP response
     */
    #[Route('/create/{id}', name: 'create', requirements: ['id' => '[1-9]\d*'], methods: 'GET|POST')]
    public function create(Request $request, Gallery $gallery): Response
    {
        $this->denyAccessUnlessGranted(ImageVoter::CREATE);

        $image = new GalleryImage();
        $image->setGallery($gallery);
        $form = $this->createForm(GalleryImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->imageRepository->save($image, true);

            $redirectTo = $this->generateUrl('talav_gallery_preview', ['id' => $gallery->getId()]);

            $this->successTrans('message.created_successfully');

            return $this->redirect($redirectTo);
        }

        return $this->render('@TalavGallery/image/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Delete action.
     *
     * @param Request      $request HTTP request
     * @param GalleryImage $image   Image entity
     *
     * @return Response HTTP response
     */
    #[Route('/delete/{id}', name: 'delete', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
    public function delete(Request $request, GalleryImage $image): Response
    {
        $this->denyAccessUnlessGranted(ImageVoter::DELETE, $image);

        $form = $this->createForm(FormType::class, $image, [
            'method' => 'DELETE',
            'action' => $this->generateUrl('talav_gallery_image_delete', ['id' => $image->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->imageRepository->delete($image);

            $this->successTrans('message.deleted_successfully');

            return $this->redirectToRoute('talav_gallery_index');
        }

        return $this->render('@TalavGallery/image/delete.html.twig', [
            'form' => $form->createView(),
            'image' => $image,
        ]);
    }

    /**
     * View action.
     *
     * @param Request $request HTTP request
     * @param GalleryImage $galleryImage Gallery entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}', name: 'view', requirements: ['id' => '[1-9]\d*'], methods: 'GET')]
    public function view(Request $request, GalleryImage $galleryImage): Response
    {
//        $this->denyAccessUnlessGranted(ImageVoter::VIEW, $galleryImage);

        /** @var UserInterface $user */
        $user = $this->getUser();

//        $page = (int) $request->query->get('page', 1);
//        $galleries = $this->createQueryBuilderPaginator(
//            $this->imageRepository->findAllByGalleryQueryBuilder($gallery),
//            $page,
//            self::PHOTO_PER_PAGE
//        );

//        $page = $request->query->getInt('page', 1);
//        $imagesPagination = $this->imageService->getPaginatedList($gallery, $page);

        return $this->render('@TalavGallery/image/view.html.twig', [
            'gallery_image' => $galleryImage,
//            'galleryImages' => $galleries,
//            'imagesPagination' => $galleries
        ]);
    }
}
