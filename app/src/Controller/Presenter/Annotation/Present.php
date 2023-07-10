<?php

declare(strict_types=1);

namespace App\Controller\Presenter\Annotation;

use App\Controller\Presenter\Contracts\PresenterInterface;

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