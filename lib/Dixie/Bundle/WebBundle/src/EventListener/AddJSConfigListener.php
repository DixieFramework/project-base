<?php

declare(strict_types=1);

namespace Talav\WebBundle\EventListener;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Talav\CoreBundle\Controller\AbstractController;
use Talav\SettingsBundle\Context\ScopeContext;
use Talav\SettingsBundle\Manager\SettingsManagerInterface;
use Talav\WebBundle\Engine\AssetBag;
use Twig\Environment;
use Zikula\Bundle\CoreBundle\HttpKernel\ZikulaKernel;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use Zikula\UsersModule\Api\ApiInterface\CurrentUserApiInterface;

class AddJSConfigListener implements EventSubscriberInterface
{
    /**
     * @var CurrentUserApiInterface
     */
    private $currentUserApi;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var AssetBag
     */
    private $footers;

    /**
     * @var string
     */
    private $defaultSessionName;

    /**
     * @var bool
     */
    private $installed;

    public function __construct(
//        string $installed,
        private readonly SettingsManagerInterface $settingsManager,
        private readonly TokenStorageInterface $tokenStorage,
        private readonly RequestStack $router,
//        CurrentUserApiInterface $currentUserApi,
        Environment $twig,
        #[Autowire('@talav_web.theme.assets_footer')] AssetBag $footers,
        string $defaultSessionName = '_zsid'
    ) {
//        $this->installed = '0.0.0' !== $installed;
//        $this->currentUserApi = $currentUserApi;
        $this->twig = $twig;
        $this->footers = $footers;
        $this->defaultSessionName = $defaultSessionName;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => [
                ['addJSConfig', -1]
            ]
        ];
    }

    /**
     * Generate a configuration for javascript and add script to site footer.
     */
    public function addJSConfig(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }
//        if (!$this->installed) {
//            return;
//        }
        $request = $event->getRequest();
        $session = $request->hasSession() ? $request->getSession() : null;

        $sJs = '';

        $sJs .= "\t\t\tvar oCore = {'core.is_admincp': " . (str_starts_with($request->get('_route') ?? 'front', 'admin') ? 'true' : 'false') . ", 'profile.user_id': " . ((string) $this->tokenStorage->getToken() ? $this->tokenStorage->getToken()->getUser()->getId() : '0') . "};\n";

        $aJsVars = [
            'sBaseURL'            => $event->getRequest()->getSchemeAndHttpHost() . '/',
            'sBaseURI'            => $event->getRequest()->getBasePath(),
//            'sJsHome'             => Phpfox::getParam('core.path_file'),
            'sJsHostname'         => isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost',
//            'sSiteName'           => Phpfox::getParam('core.site_title'),
//            'sJsStatic'           => $oAssets->getAssetUrl('PF.Base/static/', false),
//            'sJsStaticImage'      => $oAssets->getAssetUrl('PF.Base/static/image', false),
//            'sImagePath'          => $oAssets->getAssetUrl($this->getStyle('image', null, null, false, false), false),
//            'sStylePath'          => $oAssets->getAssetUrl($this->getStyle('css', null, null, false, false), false),
//            'sVersion'            => Phpfox::getId(),
//            'sJsAjax'             => Phpfox::getParam('core.path') . '_ajax/',
//            'sStaticVersion'      => $iVersion,
//            'sGetMethod'          => PHPFOX_GET_METHOD,
//            'sDateFormat'         => (defined('PHPFOX_INSTALLER') ? '' : Phpfox::getParam('core.date_field_order')),
//            'sGlobalTokenName'    => Phpfox::getTokenName(),
//            'sController'         => Phpfox_Module::instance()->getFullControllerName(),
//            'bJsIsMobile'         => false,
//            'sHostedVersionId'    => (defined('PHPFOX_IS_HOSTED_VERSION') ? PHPFOX_IS_HOSTED_VERSION : ''),
//            'sStoreUrl'           => Core\Home::store(),
//            'iLimitLoadMore'      => Phpfox::getParam('core.no_pages_for_scroll_down', 2),
//            'sJsDefaultThumbnail' => $oAssets->getAssetUrl('PF.Base/static/image/misc/thumbnail.png', false),
//            'sAssetBaseUrl'        => $oAssets->getAssetBaseUrl(),
//            'sAssetFileUrl'        => $oAssets->getAssetBaseUrl() . 'PF.Base/',
        ];

        $sJs .= "\t\t\tvar oParams = {";
        $iCnt = 0;
        foreach ($aJsVars as $sVar => $sValue) {
            $iCnt++;
            if ($iCnt != 1) {
                $sJs .= ",";
            }

            if (is_bool($sValue)) {
                $sJs .= "'{$sVar}': " . ($sValue ? 'true' : 'false');
            } else if (is_numeric($sValue)) {
                $sJs .= "'{$sVar}': " . $sValue;
            } else {
                $sJs .= "'{$sVar}': '" . str_replace("'", "\'", (string)$sValue) . "'";
            }
        }
        $sJs .= "};\n";

        $config = [
            'entrypoint' => AbstractController::HOME_PAGE,
            'baseURL' => $event->getRequest()->getSchemeAndHttpHost() . '/',
            'baseURI' => $event->getRequest()->getBasePath(),
//            'ajax_timeout' => (int) $this->settingsManager->get('ajax_timeout', ScopeContext::SCOPE_GLOBAL, null, 5000),
            'lang' => $event->getRequest()->getLocale(),
            'sessionName' => isset($session) ? $session->getName() : $this->defaultSessionName,
            'uid' => (string) $this->tokenStorage->getToken() ? $this->tokenStorage->getToken()->getUser()->getId() : '0'
//            'uid' => (int) $this->currentUserApi->get('uid')
        ];

        $config = array_map('htmlspecialchars', $config);
        $content = $this->twig->render('@TalavWeb/_partials/js_config.html.twig', [
            'config' => $config,
            'js' => $sJs
        ]);
        $this->footers->add([$content => 0]);
    }
}
