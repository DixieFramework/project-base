<?php

declare(strict_types=1);

namespace Talav\UserBundle\Enum;

/**
 * Flash bag type enumeration.
 */
enum UserFlagKey: string
{
    case ASSIGNED_TASK_NOTIFICATION = 'assignedTaskNotification';
    case DRAFT_STATEMENT_SUBMISSION_REMINDER_ENABLED = 'draftStatementSubmissionReminderEnabled';
    case NO_USER_TRACKING = 'noPiwik';
    case SUBSCRIBED_TO_NEWSLETTER = 'newsletter';
    case WANTS_FORUM_NOTIFICATIONS = 'forumNotification';
    case IS_NEW_USER = 'newUser';
    case PROFILE_COMPLETED = 'profileCompleted';
    case ACCESS_CONFIRMED = 'access_confirmed';
    case INVITED = 'invited';
}
