<?php

namespace App\Controller\Presenter\Contracts;

interface PresenterInterface
{
    public function present(mixed $data): mixed;
}