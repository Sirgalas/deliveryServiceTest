<?php

namespace App\Framework\Controller\Attributes\Ruote\TargetResourse\Contracts;

interface ValueConverterInterface
{
    public function convert(mixed $value): mixed;
}