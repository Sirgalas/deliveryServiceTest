<?php

namespace App\Doctrine\ORM\Contracts;

use Symfony\Component\Uid\Uuid;

interface Identity
{
    public function getId():Uuid;
}