<?php

namespace App\Controller\Attributes\Ruote\TargetResourse\Contracts;

interface ValueConverterInterface
{
    public function convert(mixed $value): mixed;
}