<?php

namespace App\Framework\Doctrine\ORM\Contracts;

use Symfony\Component\Uid\Uuid;

interface Identity
{
    public function getId():Uuid;
}