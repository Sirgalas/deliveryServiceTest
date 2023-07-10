<?php

declare(strict_types=1);

namespace App\Controller\Attributes\Ruote\TargetResourse\Annotation;

use App\Controller\Attributes\Ruote\TargetResourse\Contracts\ValueConverterInterface;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class TargetResource
{
    public function __construct(
        public string $table,
        public string $id = 'id',
        public string $attributeName = 'id',
        public array $criteria = [],
        public ?ValueConverterInterface $converter = null
    ) {
    }
}