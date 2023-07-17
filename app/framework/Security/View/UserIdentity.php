<?php

declare(strict_types=1);

namespace App\Framework\Security\View;

use App\Framework\Security\Contracts\IdentityInterface;
use App\Framework\Dto\AbstractCommand;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface as PasswordInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

class UserIdentity extends AbstractCommand implements IdentityInterface,UserInterface, PasswordInterface
{
    /** Идентификатор пользователя. */
    #[Groups(['full'])]
    public Uuid $id;

    /** Почтовый адрес. */
    #[Groups(['full'])]
    public string $email;

    /** Password hash. */
    #[Groups(['debug'])]
    public string $password_hash;

    /** Контекст (роли и тд.). */
    #[Groups(['full'])]
    #[OA\Property(ref: new Model(type: ContextCommand::class))]
    public ContextCommand $context;

    public function setContext(string | array $jsonOrArray): void
    {
        $this->context = new ContextCommand($this->getArray($jsonOrArray));
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getPassword(): ?string
    {
        return $this->password_hash;
    }

    public function getRoles(): array
    {
        return $this->context->getRoles();
    }

    public function hasOneOfContextRoles(array $roles): bool
    {
        return [] !== array_intersect($this->getRoles(), $roles);
    }
}