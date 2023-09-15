<?php

declare(strict_types=1);

namespace Talav\Component\Resource\Model;

/**
 * TimestampableInterface should be implemented by classes which needs to be
 * identified as Timestampable.
 */
interface TimestampableInterface
{
    /**
     * Gets the created at datetime.
     *
     * @return \DateTime The DateTime instance
     */
    public function getCreatedAt();

    /**
     * Sets the created at datetime.
     *
     * @param \DateTime $createdAt The DateTime instance
     */
    public function setCreatedAt(\DateTime $createdAt);

    /**
     * Gets the updated at datetime.
     *
     * @return \DateTime The DateTime instance
     */
    public function getUpdatedAt();

    /**
     * Sets the updated at datetime.
     *
     * @param \DateTime $updatedAt The DateTime instance
     */
    public function setUpdatedAt(\DateTime $updatedAt);
}
