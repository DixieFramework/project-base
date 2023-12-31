<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Service;

/**
 * Service to generate a nonce value.
 */
class NonceService
{
    /**
     *  The generated nonce.
     */
    private ?string $nonce = null;

    /**
     * Constructor.
     *
     * @param positive-int $length the length of generated bytes
     */
    public function __construct(private readonly int $length = 16)
    {
    }

    /**
     * Gets the CSP nonce.
     *
     * @psalm-param positive-int $length
     *
     * @throws \Exception
     */
    public function getCspNonce(int $length = null): string
    {
        return \sprintf("'nonce-%s'", $this->getNonce($length));
    }

    /**
     * Generates a random nonce parameter.
     *
     * @psalm-param positive-int $length
     *
     * @throws \Exception
     */
    public function getNonce(int $length = null): string
    {
        if (null === $this->nonce) {
            $this->nonce = \bin2hex(\random_bytes($length ?? $this->length));
        }

        return $this->nonce;
    }
}
