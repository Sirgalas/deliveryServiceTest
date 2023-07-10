<?php

declare(strict_types=1);

namespace App\Entity\Order\UseCase\Order\Delete;

use App\Doctrine\ORM\Flusher;
use App\Entity\Order\Domain\Entity\Order;
use App\Entity\Order\Domain\Repository\OrderRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler
{
    public function __construct(
        readonly OrderRepository $repository,
        readonly Flusher $flusher
    ) {
    }

    public function handle(string $order_id ,bool $flush = false): void
    {
        $order = $this->repository->find($order_id);
        if(!$order instanceof Order) {
            throw new NotFoundHttpException('not found');
        }

        $this->repository->remove($order);

        if($flush) {
            $this->flusher->flush();
        }
    }
}