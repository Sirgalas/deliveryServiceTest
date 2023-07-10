<?php

declare(strict_types=1);

namespace App\Security\Voter\Admin;

use App\Security\Role\Role;
use App\Security\View\UserIdentity;
use App\Security\Voter\AbstractVoter;

final class Access extends AbstractVoter
{
    protected array $accessRoles = Role::ADMIN_ROLES;

    public function hasContext(mixed $subject, UserIdentity $user): bool
    {
        return true;
    }
}