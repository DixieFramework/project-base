<?php
//
//use Groshy\Kernel;
//
//require_once dirname(__DIR__).'/vendor/autoload_runtime.php';
//
//return function (array $context) {
//    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
//};


declare(strict_types=1);

use Groshy\Kernel;
use AnzuSystems\CommonBundle\Kernel\AnzuKernel;

require_once dirname(__DIR__) . '/vendor/autoload_runtime.php';

return static function (array $context): Kernel {
    return new Kernel(
        appNamespace: $context['APP_NAMESPACE'],
        appSystem: $context['APP_SYSTEM'],
        appVersion: $context['APP_VERSION'],
        appReadOnlyMode: (bool)$context['APP_READ_ONLY_MODE'],
        environment: $context['APP_ENV'],
        debug: (bool)$context['APP_DEBUG'],
    );
};
