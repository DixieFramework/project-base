<?php

declare(strict_types=1);

namespace Talav\CoreBundle\HttpKernel\Bundle\Installer;

use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\Output;

class AbstractInstaller implements InstallerInterface
{
    protected BufferedOutput $output;

    public function __construct()
    {
        $this->output = new BufferedOutput(Output::VERBOSITY_NORMAL, true);
    }

    public function install(): void
    {
    }

    public function uninstall(): void
    {
    }

    public function isInstalled(): bool
    {
        return true;
    }

    public function canBeInstalled(): bool
    {
        return false;
    }

    public function canBeUninstalled(): bool
    {
        return false;
    }

    public function needsReloadAfterInstall(): bool
    {
        return false;
    }

    public function getOutput(): BufferedOutput | NullOutput
    {
        return $this->output;
    }
}
