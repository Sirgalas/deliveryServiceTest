<?php

declare(strict_types=1);

namespace App\Test\Functional\Dto;

use App\Dto\AbstractCommand;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class Response extends AbstractCommand
{
    public int $code;
    public string $type;
    public array $content = [];
    public ResponseHeaderBag $headers;

    public function setContent(mixed $content): void
    {
        if (!\is_array($content)) {
            $content = [$content];
        }

        $this->content = $content;
    }
}