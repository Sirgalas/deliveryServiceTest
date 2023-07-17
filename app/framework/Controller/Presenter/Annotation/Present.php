<?php

declare(strict_types=1);

namespace App\Framework\Controller\Presenter\Annotation;

use App\Framework\Controller\Presenter\Contracts\PresenterInterface;

#[\Attribute]
class Present
{
    /**
     * @param class-string<PresenterInterface> $presenter
     */
    public function __construct(public string $presenter, public ?string $sourcePropertyName = null)
    {
    }
}