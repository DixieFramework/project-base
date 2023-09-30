<?php

declare(strict_types=1);

namespace Talav\GalleryBundle\Controller;

use Talav\Component\Resource\Manager\ManagerInterface;
use Talav\CoreBundle\Controller\AbstractController;
use Talav\GalleryBundle\Entity\Gallery;
use Talav\GalleryBundle\Entity\GalleryImage;
use Talav\GalleryBundle\Form\Type\GalleryImageType;
use Talav\GalleryBundle\Voter\ImageVoter;
use Talav\GalleryBundle\Service\ImageServiceInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Talav\GalleryBundle\Repository\GalleryImageRepository;

/**
 * Class ImagesController.
 */
#[Route('/gallery/image', name: 'talav_gallery_image_')]
class GalleryImageController extends AbstractController
{
    private GalleryImageRepository $imageRepository;


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
}
