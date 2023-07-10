<?php

declare(strict_types=1);

namespace App\Security\View;

use App\Dto\AbstractCommand;
use App\Security\Role\Role;
use Symfony\Component\Serializer\Annotation\Groups;

class ContextCommand extends AbstractCommand
{
    /**
     * Роли.
     *
     * @var array<array-key, string>
     */
    #[Groups('full')]
    public array $roles = [];

    public function setRoles(string|array $jsonOrArray): void
    {
        /** @var string $value */
        foreach ($this->getArray($jsonOrArray) as $value) {
            $this->roles[] = Role::ALL[$value];
        }
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return array_unique($this->roles);
    }
}