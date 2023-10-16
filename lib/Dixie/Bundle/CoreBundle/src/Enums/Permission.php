<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Enums;

enum Permission: string
{
    case COMMENT_ADD = 'comment';
    case COMMENT_VIEW = 'comment_view';
    case COMMENT_UPDATE = 'comment_update';
    case COMMENT_DELETE = 'comment_delete';

    case FRAMEWORK_CREATE = 'framework_create';
    case FRAMEWORK_LIST = 'list';
    case FRAMEWORK_VIEW = 'view';
    case FRAMEWORK_EDIT = 'edit';
    case FRAMEWORK_EDIT_ALL = 'framework_edit_all';
    case FRAMEWORK_DELETE = 'delete';

    case FRAMEWORK_DOWNLOAD_EXCEL = 'framework_download_excel';

    case ITEM_EDIT = 'item_edit';
    case ITEM_ADD_TO = 'add-standard-to';

    case ASSOCIATION_ADD_TO = 'add-association-to';
    case ASSOCIATION_EDIT = 'association_edit';

    case ADDITIONAL_FIELDS_MANAGE = 'manage_additional_fields';

    case MANAGE_MIRRORS = 'manage_mirrors';

    case MANAGE_ORGANIZATIONS = 'manage_organizations';

    case MANAGE_USERS = 'manage_users';
    case MANAGE_THIS_USER = 'manage_user';
    case MANAGE_ALL_USERS = 'manage_all_users';

    case MANAGE_EDITORS = 'manage_editors';

    case MANAGE_SYSTEM_LOGS = 'manage_system_logs';

    case FEATURE_DEV_ENV_CHECK = 'feature_dev_env';
}
