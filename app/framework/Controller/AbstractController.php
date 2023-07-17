<?php

declare(strict_types=1);

namespace App\Framework\Controller;

use App\Framework\Controller\Presenter\Presenter;
use App\Framework\Dto\AbstractCommand;
use App\Framework\Security\View\UserIdentity;
use App\Framework\Security\Validator\Framework\Problem\ValidationProblem;
use Dompdf\Dompdf;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPaginationInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Phpro\ApiProblem\Exception\ApiProblemException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as SymfonyAbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Webmozart\Assert\Assert;

class AbstractController  extends SymfonyAbstractController
{
    public function __construct(
        private readonly Presenter $presenter,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator
    ) {
    }

    protected function pagination(
        SlidingPaginationInterface | PaginationInterface $paginator,
        int $status = Response::HTTP_OK,
        array $headers = [],
        array $context = [],
        array $meta = [],
        array $errors = []
    ): JsonResponse {
        $meta = array_merge(
            $meta,
            $paginator instanceof SlidingPaginationInterface ? ($paginator->getCustomParameters() ?? []) : [],
            [
                'total' => null,
                'page' => $paginator->getCurrentPageNumber(),
                'page_size' => $paginator->getItemNumberPerPage(),
            ]
        );

        if ($this->isAuthenticated()) {
            $meta = array_merge($meta, ['roles' => $this->getUser()->getRoles()]);
        }

        if (0 !== $paginator->getTotalItemCount()) {
            $meta['total'] = $paginator->getTotalItemCount();
        }

        $items = $paginator->getItems();
        $this->present($items);

        $data = [
            'data' => $items,
            'errors' => $errors,
            'meta' => $meta,
        ];

        return new JsonResponse(
            $this->serializer->serialize($data, 'json', $context),
            $status,
            $headers + ['Content-Type' => 'application/vnd.api+json'],
            true
        );
    }

    /** {@inheritdoc} */
    protected function json(
        mixed $data = [],
        int $status = Response::HTTP_OK,
        array $headers = [],
        array $context = [],
        array $meta = [],
        array $errors = []
    ): JsonResponse {
        $this->present($data);

        if (\is_array($data)) {
            $meta = array_merge($meta, ['total' => \count($data)]);
        }

        if ($this->isAuthenticated()) {
            $meta = array_merge($meta, ['roles' => $this->getUser()->getRoles()]);
        }

        $data = [
            'data' => $data,
            'errors' => $errors,
            'meta' => $meta,
        ];

        return new JsonResponse(
            $this->serializer->serialize($data, 'json', $context),
            $status,
            $headers + ['Content-Type' => 'application/vnd.api+json'],
            true
        );
    }

    protected function pdf(string $html, string $filename, array $options = []): StreamedResponse
    {
        $response = new StreamedResponse();
        $response->setCallback(function () use ($html, $filename, $options): void {
            $pdf = new Dompdf();
            $pdf->loadHtml($html, 'UTF-8');
            $pdf->render();

            $pdf->stream($filename, $options);
        });

        return $response;
    }

    protected function validate(AbstractCommand $command): void
    {
        $violationList = $this->validator->validate($command);

        if (0 !== $violationList->count()) {
            throw new ApiProblemException(new ValidationProblem($violationList));
        }
    }

    protected function isAuthenticated(): bool
    {
        return parent::getUser() instanceof UserInterface;
    }

    protected function getUser(): UserIdentity
    {
        /** @var UserIdentity $user */
        $user = parent::getUser();
        Assert::isInstanceOf($user, UserIdentity::class, 'Security system not supported this endpoint.');

        return $user;
    }

    private function present(mixed $contents): void
    {
        if (!is_iterable($contents)) {
            $contents = [$contents];
        }

        /** @var mixed $content */
        foreach ($contents as $content) {
            if ($content instanceof AbstractCommand) {
                $this->presenter->present($content);
            }
        }
    }
}