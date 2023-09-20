<?php

declare(strict_types=1);

namespace Talav\SettingsBundle\Tests\Functional;

use Doctrine\ORM\Tools\SchemaTool;
use Liip\FunctionalTestBundle\Test\WebTestCase as BaseWebTestCase;
use Talav\SettingsBundle\Tests\Functional\app\AppKernel;

class WebTestCase extends BaseWebTestCase
{
    protected $manager;

    protected static function getKernelClass()
    {
        require_once __DIR__.'/app/AppKernel.php';

        return AppKernel::class;
    }

    protected function initDatabase()
    {
        $kernel = $this->createKernel();
        $kernel->boot();
        $this->manager = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $schemaTool = new SchemaTool($this->manager);
        $metadata = $this->manager->getMetadataFactory()->getAllMetadata();
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
    }

    public function testService()
    {
        self::assertTrue($this->getContainer()->has('swp_settings.manager.settings'));
    }
}
