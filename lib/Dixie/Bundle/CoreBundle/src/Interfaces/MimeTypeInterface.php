<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Interfaces;

/**
 * Class implementing this interface provides function to download document.
 */
interface MimeTypeInterface
{
    /**
     * Gets the attachment mime type.
     */
    public function getAttachmentMimeType(): string;

    /**
     * Gets the default file extension (without the dot separator).
     */
    public function getFileExtension(): string;

    /**
     * Gets the inline mime type.
     */
    public function getInlineMimeType(): string;
}
