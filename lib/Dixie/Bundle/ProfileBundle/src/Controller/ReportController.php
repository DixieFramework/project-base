<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Controller;

use Groshy\Entity\Profile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Talav\Component\Resource\Manager\ManagerInterface;
use Talav\CoreBundle\Controller\AbstractController;
use Talav\CoreBundle\Interfaces\RoleInterface;
use Talav\ProfileBundle\Entity\ProfileInterface;

#[AsController]
#[Route(path: '/report', name: 'profile_report_')]
#[IsGranted(RoleInterface::ROLE_USER)]
class ReportController extends AbstractController
{
	#[Route('/', name: 'index', methods: ['GET'])]
    public function indexAction(Request $request): Response
    {
        return new Response('Hello world from talav_profile');
    }

	#[Route('/reportProfile/{id}', name: 'report_profile', methods: ['GET'])]
	#[ParamConverter('profile', class: Profile::class, options: ['mapping' => ['id' => 'id']])]
	#[Security('is_granted("ROLE_USER")')]
	public function reportProfile(Profile $profile, ManagerInterface $reportManager): Response
	{
		$exist = $reportManager->getRepository()->findOneBy(['sender' => $this->getUser()->getProfile(), 'profile' => $profile]);

		if ($exist) {
			$exist->setSeen(false);
			$reportManager->flush();
		} else {
			$report = $reportManager->create();
			$report->setProfile($profile);
			$report->setSender($this->getUser()->getProfile());
			$report->setAccused($profile);
			$reportManager->update($report, true);
		}

		$this->addFlash('success', $this->trans('flash.report.is.sent'));

		return $this->redirectToRoute('user_profile_view', [
			'id' => $profile->getId(),
			'username' => $profile->getUser()->getUsername()
		]);
	}
}
