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
 * Controller to output policy information.
 */
#[AsController]
#[Route(path: '/about/policy')]
class AboutPolicyController extends AbstractController
{
    #[IsGranted(RoleInterface::ROLE_USER)]
    #[Route(path: '/content', name: 'about_policy_content')]
    public function content(): JsonResponse
    {
        $parameters = [
            'comments' => true,
            'link' => false,
        ];
        $content = $this->renderView('@TalavWeb/about/policy_content.html.twig', $parameters);

        return $this->jsonTrue(['content' => $content]);
    }

    #[IsGranted(AuthenticatedVoter::PUBLIC_ACCESS)]
    #[Route(path: '', name: 'about_policy')]
    public function index(): Response
    {
        $parameters = [
            'comments' => false,
            'link' => true,
        ];

        return $this->render('@TalavWeb/about/policy.html.twig', $parameters);
    }

    #[IsGranted(AuthenticatedVoter::PUBLIC_ACCESS)]
    #[Route(path: '/pdf', name: 'about_policy_pdf')]
    public function pdf(): PdfResponse
    {
        $parameters = [
            'comments' => false,
            'link' => false,
        ];
        $content = $this->renderView('about/policy_content.html.twig', $parameters);
        $report = new HtmlReport($this, $content);
        $report->setTitleTrans('about.policy', [], true);

        return $this->renderPdfDocument($report);
    }

    /**
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \PhpOffice\PhpWord\Exception\Exception
     */
    #[IsGranted(RoleInterface::ROLE_USER)]
    #[Route(path: '/word', name: 'about_policy_word')]
    public function word(): WordResponse
    {
        $parameters = [
            'comments' => false,
            'link' => false,
        ];
        $content = $this->renderView('about/policy_content.html.twig', $parameters);
        $doc = new HtmlDocument($this, $content);
        $doc->setTitleTrans('about.policy');

        return $this->renderWordDocument($doc);
    }
}
