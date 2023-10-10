<?php

declare(strict_types=1);

namespace Talav\WebBundle\Service;

use DateTime;
use Exception;
use Symfony\Component\Security\Core\Security;
use Talav\Component\Resource\Manager\ManagerInterface;
use Talav\Component\User\Model\UserInterface;

class AntispamService
{
    private const DELAY_MESSAGE = 60;

    private const DELAY_COMMENT = 90;

    public function __construct(private readonly ManagerInterface $commentManager, private readonly Security $security)
    {
    }

    /**
     * @throws Exception
     */
    public function canPostComment(UserInterface $user): bool
    {
        $lastComment = $this->commentManager->getRepository()->findLastCommentByUser($user);

        if ($lastComment && !$this->security->isGranted('ROLE_MODERATOR')) {
            $currentDate = new DateTime();

            return $currentDate->modify(sprintf('-%s seconds', self::DELAY_COMMENT)) > $lastComment->getPublishedAt();
        }

        return true;
    }

    /**
     * @throws Exception
     */
    public function canPostMessage(User $user): bool
    {
        $lastMessage = $this->messageRepository->findLastMessageByUser($user);

        if ($lastMessage && !$this->security->isGranted('ROLE_MODERATOR')) {
            $currentDate = new DateTime();

            return $currentDate->modify(sprintf('-%s seconds', self::DELAY_MESSAGE)) > $lastMessage->getCreatedAt();
        }

        return true;
    }
}
