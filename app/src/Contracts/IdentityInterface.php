<?php

namespace App\Contracts;

use Symfony\Component\Uid\Uuid;

interface IdentityInterface
{
    public function getId(): Uuid;
}