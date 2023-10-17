<?php

declare(strict_types=1);

namespace Talav\CoreBundle\HttpKernel\Bundle;

use Talav\CoreBundle\HttpKernel\Bundle\Installer\InstallerInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

interface TalavBundleInterface extends BundleInterface
{
    /**
     * Bundle name as shown in extension manager
     *
     */
    public function getNiceName(): string;

    /**
     * Bundle description as shown in extension manager
     *
     */
    public function getDescription(): string;

    /**
     * Bundle version as shown in extension manager
     *
     */
    public function getVersion(): string;

    /**
     * If the bundle has an installation routine, an installer is responsible of handling installation related tasks
     *
     */
    public function getInstaller(): ?InstallerInterface;
}
