<?php

declare(strict_types=1);

namespace App\Framework\Validator\Problem;

use Phpro\ApiProblem\Http\HttpApiProblem;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationProblem extends HttpApiProblem
{
    final public const TYPE_SYMFONY_VIOLATIONS = 'https://symfony.com/errors/validation';

    public function __construct(ConstraintViolationListInterface $violationList)
    {
        parent::__construct(
            400,
            [
                'type' => self::TYPE_SYMFONY_VIOLATIONS,
                'title' => 'Validation Failed',
                'detail' => $this->parseMessages($violationList),
                'violations' => $this->serializeViolations($violationList),
            ]
        );
    }

    private function parseMessages(ConstraintViolationListInterface $violationList): string
    {
        $messages = [];

        foreach ($violationList as $violation) {
            $propertyPath = $violation->getPropertyPath();
            $prefix = mb_strlen($propertyPath) > 0 ? sprintf('%s: ', $propertyPath) : '';
            $messages[] = $prefix . $violation->getMessage();
        }

        return implode("\n", $messages);
    }

    private function serializeViolations(ConstraintViolationListInterface $violationList): array
    {
        $violations = [];

        foreach ($violationList as $violation) {
            $violations['messages'][$violation->getPropertyPath()][] = $violation->getMessage();
        }

        return $violations;
    }
}