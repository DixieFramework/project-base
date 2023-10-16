<?php

declare(strict_types=1);

namespace Talav\AdminBundle\Traits;

use Symfony\Component\HttpFoundation\Request;
use Talav\CoreBundle\Entity\Interfaces\EntityInterface;

trait DeleteCsrfTrait
{
    public function isDeleteCsrfTokenValid(EntityInterface $entity): bool
    {
        $id = $entity->getId();
        $token = (string) $this->getCurrentRequest()->request->get('_token');

        if ($this->getCurrentRequest()->isXmlHttpRequest()) {
            /** @var array $content */
            $content = json_decode($this->getCurrentRequest()->getContent(), associative: true);
            $token = (string) $content['_token'];
        }

        return $this->isCsrfTokenValid("delete_{$id}", $token);
    }
}
