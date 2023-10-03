<?php

declare(strict_types=1);

namespace Talav\Bundle\CoreBundle\Utils;

use Psr\Container\ContainerInterface;
use Talav\Bundle\CoreBundle\Utils\Type\ArrayUtil;
use Talav\Bundle\CoreBundle\Utils\Type\StringUtil;

class Utils extends AbstractServiceSubscriber
{
    /**
     * @var ContainerInterface
     */
    protected $locator;

    /**
     * Utils constructor.
     */
    public function __construct(ContainerInterface $locator)
    {
        $this->locator = $locator;
    }

    public function array(): ArrayUtil
    {
        return $this->locator->get(ArrayUtil::class);
    }

    public static function getSubscribedServices(): array
    {
        return [
//            AccordionUtil::class,
            ArrayUtil::class,
//            ContainerUtil::class,
//            DcaUtil::class,
//            FileUtil::class,
//            HtmlUtil::class,
//            LocaleUtil::class,
//            ModelUtil::class,
//            RequestUtil::class,
//            RoutingUtil::class,
//            StringUtil::class,
//            UrlUtil::class,
//            UserUtil::class,
        ];
    }
}
