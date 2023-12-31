<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Command;

use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Trait to write output messages.
 */
trait LoggerTrait
{
    /**
     * The symfony style.
     */
    private ?SymfonyStyle $io = null;

    /**
     * Returns whether verbosity is verbose (-v).
     */
    protected function isVerbose(): bool
    {
        return $this->io?->isVerbose() ?? false;
    }

    /**
     * Returns whether verbosity is very verbose (-vv).
     */
    protected function isVeryVerbose(): bool
    {
        return $this->io?->isVeryVerbose() ?? false;
    }

    /**
     * Writes the given information message.
     */
    protected function write(string $message, string $style = 'info'): void
    {
        $this->io?->writeln("<$style>$message</>");
    }

    /**
     * Writes the given error message.
     */
    protected function writeError(string $message): void
    {
        $this->io?->error($message);
    }

    /**
     * Writes the given note message.
     */
    protected function writeNote(string $message): void
    {
        $this->io?->note($message);
    }

    /**
     * Writes the given success message.
     */
    protected function writeSuccess(string $message): void
    {
        $this->io?->success($message);
    }

    /**
     * Writes the given information message whether verbosity is verbose (-v).
     */
    protected function writeVerbose(string $message, string $style = 'info'): void
    {
        if ($this->isVerbose()) {
            $this->write($message, $style);
        }
    }

    /**
     * Writes the given information message whether verbosity is very verbose (-vv).
     */
    protected function writeVeryVerbose(string $message, string $style = 'info'): void
    {
        if ($this->isVeryVerbose()) {
            $this->write($message, $style);
        }
    }

    /**
     * Writes the given warning message.
     */
    protected function writeWarning(string $message): void
    {
        $this->io?->warning($message);
    }
}
