<?php

declare(strict_types=1);

namespace Talav\GalleryBundle\Controller;

use Talav\Component\Resource\Manager\ManagerInterface;
use Talav\GalleryBundle\Entity\Gallery;
use Talav\GalleryBundle\Entity\Image;
use Talav\GalleryBundle\Form\Type\ImageType;
use Talav\GalleryBundle\Voter\ImageVoter;
use Talav\GalleryBundle\Service\ImageServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Talav\GalleryBundle\Repository\ImageRepository;

/**
 * Class ImagesController.
 */
#[Route('/gallery-image')]
class ImageController extends AbstractController
{
    private ImageRepository $imageRepository;

    /**
     * Constructor.
     *
     * @param ImageServiceInterface $imageRepository Image service
     * @param TranslatorInterface   $translator   Translator
     */
//    public function __construct(private ImageServiceInterface $imageService, private TranslatorInterface $translator)
    public function __construct(private readonly ManagerInterface $galleryImageManager, private TranslatorInterface $translator)
    {
        $this->imageRepository = $galleryImageManager->getRepository();
    }

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     * @param Gallery $gallery Gallery entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/create/{id}',
        name: 'image_create',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET|POST',
    )]
    public function create(Request $request, Gallery $gallery): Response
    {
        $this->denyAccessUnlessGranted(ImageVoter::CREATE);

        $image = new Image();
        $image->setGallery($gallery);
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->imageRepository->save($image);

            $redirectTo = $this->generateUrl('gallery_preview', ['id' => $gallery->getId()]);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirect($redirectTo);
        }

        return $this->render(
            'images/create.html.twig',
            ['form' => $form->createView()],
        );
    }

    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param Image   $image   Image entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/delete/{id}',
        name: 'image_delete',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET|DELETE',
    )]
    public function delete(Request $request, Image $image): Response
    {
        $this->denyAccessUnlessGranted(ImageVoter::DELETE, $image);

        $form = $this->createForm(FormType::class, $image, [
            'method' => 'DELETE',
            'action' => $this->generateUrl('image_delete', ['id' => $image->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->imageRepository->delete($image);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('talav_gallery_index');
        }

        return $this->render(
            'images/delete.html.twig',
            [
                'form' => $form->createView(),
                'image' => $image,
            ]
        );
    }
}
