<?php

namespace App\Framework\Security\Contracts;

use Symfony\Component\Uid\Uuid;

interface IdentityInterface
{
    public function getId(): Uuid;
}