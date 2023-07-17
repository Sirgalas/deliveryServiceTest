<?php

declare(strict_types=1);

namespace App\Framework\Security\Voter\Admin;

use App\Framework\Security\Role\Role;
use App\Framework\Security\View\UserIdentity;
use App\Framework\Security\Voter\AbstractVoter;

final class Access extends AbstractVoter
{
    protected array $accessRoles = Role::ADMIN_ROLES;

    public function hasContext(mixed $subject, UserIdentity $user): bool
    {
        return true;
    }
}