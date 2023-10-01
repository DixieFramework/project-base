<?php

declare(strict_types=1);

namespace Talav\UserBundle\EventSubscriber;

use demosplan\DemosPlanCoreBundle\Entity\User\Role;
use demosplan\DemosPlanCoreBundle\Logic\User\CurrentUserInterface;
use Psr\Log\LoggerInterface;
use Talav\CoreBundle\Interfaces\DisableListenerInterface;
use Talav\CoreBundle\Traits\DisableListenerTrait;
use Talav\CoreBundle\Traits\TranslatorFlashMessageAwareTrait;
use Talav\Component\User\Model\UserInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Symfony\Contracts\Service\ServiceSubscriberTrait;

class CheckProfileDataMissingSubscriber implements EventSubscriberInterface, ServiceSubscriberInterface, DisableListenerInterface
{
	use DisableListenerTrait;
    use ServiceSubscriberTrait;
    use TranslatorFlashMessageAwareTrait;

    /**
     * Some routes may not be checked as it leads to infinite loops and the like.
     */
    private const EXCLUDED_ROUTES = [
        'homepage',
        'user_profile_edit',
	    'talav_user_login',
	    'talav_user_logout',
//        'core_file',
//        'core_file_procedure',
//        'DemosPlan_user_complete_data',
//        'DemosPlan_user_logout',
//        'user_update_additional_information',
    ];

    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(private Security $security, LoggerInterface $logger, RouterInterface $router)
    {
        $this->logger = $logger;
        $this->router = $router;

		$this->setEnabled(false);
    }

    public function onKernelRequest(RequestEvent $event): void
    {
		if (!$this->isEnabled()) {
			return;
		}

        $request = $event->getRequest();

        $user = $this->security->getUser();
        if (!$user instanceof UserInterface) {
            return;
        }

        $user->setProfileCompleted(false);

        // citizens should never need to complete orga data
        if ($user->hasRole('ROLE_ADMIN')) {
            //return;
        }

        // check whether all mandatory organisation data is given
        // ignore routes, that need to be called even if not all data is provided
        $route = $request->attributes->get('_route');
//        $this->logger->debug('checkUser: Route to check OrgadataMissing', [$route]);
//        $this->logger->debug('checkUser: User from Session to check OrgadataMissing', ['userName' => $user->getName()]);

//        dd($user->isProfileCompleted());

        if (!in_array($route, self::EXCLUDED_ROUTES, true) && false === $user->isProfileCompleted()) {
//            $this->logger->info('checkUser: Userdata not completed', ['userName' => $user->getName()]);

            $this->notify($user);

            $event->setResponse(new RedirectResponse($this->router->generate('user_profile_edit')));
        }
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents(): array
    {
        return [
            // must be registered before (i.e. with a higher priority than) the default Locale listener
            //KernelEvents::REQUEST => 'onKernelRequest',
            KernelEvents::REQUEST => ['onKernelRequest', 0],
        ];
    }

    /**
     * Notify the success login to the user.
     */
    private function notify(UserInterface $user): void
    {
        $params = [
            '%user_name%' => $user->getUserIdentifier()
        ];

//        $this->infoTrans('profile.edit.flash.complete_profile', $params);
        $this->infoTrans('security.login.success', $params);
    }
}
