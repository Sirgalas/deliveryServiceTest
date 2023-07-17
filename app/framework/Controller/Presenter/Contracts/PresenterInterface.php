<?php

namespace App\Framework\Controller\Presenter\Contracts;

interface PresenterInterface
{
    public function present(mixed $data): mixed;
}