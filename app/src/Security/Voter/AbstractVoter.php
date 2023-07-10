<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Doctrine\Dbal\Fetcher;
use App\Security\Role\Role;
use App\Security\View\UserIdentity;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter as BaseVoter;

abstract class AbstractVoter extends BaseVoter
{
    final public const MESSAGE = 'Извините, но у вас недостаточно прав для выполнения этого действия.';
    protected array $accessRoles = [];

    public function __construct(protected Fetcher $fetcher)
    {
    }

    /**
     * Метод в котором при необходимости проверяем контекст.
     */
    abstract public function hasContext(mixed $subject, UserIdentity $user): bool;

    /**
     * Пользователь аутентифицирован, имеет заявленные роли, подходящий контекст.
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        return $user instanceof UserIdentity
            && $user->hasOneOfContextRoles(array_merge($this->accessRoles, [Role::ROLE_SUPER_ADMIN]))
            && $this->hasContext($subject, $user);
    }

    /**
     * {@inheritdoc}
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        return $this::class === $attribute;
    }
}
