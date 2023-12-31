<?php

declare(strict_types=1);

namespace Talav\WebBundle\Controller;

use Talav\CoreBundle\Controller\AbstractController;
use Talav\CoreBundle\Interfaces\RoleInterface;
use App\Report\HtmlReport;
use App\Response\PdfResponse;
use App\Response\WordResponse;
use App\Word\HtmlDocument;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Controller to output licence information.
 */
#[AsController]
#[Route(path: '/about/licence')]
class AboutLicenceController extends AbstractController
{
    #[IsGranted(RoleInterface::ROLE_USER)]
    #[Route(path: '/content', name: 'about_licence_content')]
    public function content(): JsonResponse
    {
        $content = $this->renderView('@TalavWeb/about/licence_content.html.twig');

        return $this->jsonTrue(['content' => $content]);
    }

    #[IsGranted(AuthenticatedVoter::PUBLIC_ACCESS)]
    #[Route(path: '', name: 'about_licence')]
    public function index(): Response
    {
        return $this->render('@TalavWeb/about/licence.html.twig', ['link' => true]);
    }

    #[IsGranted(AuthenticatedVoter::PUBLIC_ACCESS)]
    #[Route(path: '/pdf', name: 'about_licence_pdf')]
    public function pdf(): PdfResponse
    {
        $content = $this->renderView('about/licence_content.html.twig', ['link' => false]);
        $report = new HtmlReport($this, $content);
        $report->setTitleTrans('about.licence', [], true);

        return $this->renderPdfDocument($report);
    }

    /**
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \PhpOffice\PhpWord\Exception\Exception
     */
    #[IsGranted(RoleInterface::ROLE_USER)]
    #[Route(path: '/word', name: 'about_licence_word')]
    public function word(): WordResponse
    {
        $content = $this->renderView('about/licence_content.html.twig', ['link' => false]);
        $doc = new HtmlDocument($this, $content);
        $doc->setTitleTrans('about.licence');

        return $this->renderWordDocument($doc);
    }
}
