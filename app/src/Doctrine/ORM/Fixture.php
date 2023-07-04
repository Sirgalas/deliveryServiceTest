<?php

declare(strict_types=1);

namespace App\Doctrine\ORM;

use Faker\Factory;
use Faker\Generator;

abstract class Fixture extends \Doctrine\Bundle\FixturesBundle\Fixture
{
    /** Data generator. */
    public function dg(): Generator
    {
        return Factory::create();
    }
}