<?php

declare(strict_types=1);

namespace App\Framework\Controller\Presenter;

use App\Framework\Controller\Presenter\Annotation\Present;
use App\Framework\Controller\Presenter\Contracts\PresenterInterface;
use App\Framework\Dto\AbstractCommand;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Presenter
{
    use ContainerAwareTrait;

    public function present(AbstractCommand $command): void
    {
        $this->handleAttributes($command);
    }

    private function handleAttributes(AbstractCommand $command): void
    {
        $reflectionClass = new \ReflectionClass($command);

        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            if ($this->isArray($reflectionProperty)) {
                /** @var mixed $value */
                foreach ($reflectionProperty->getValue($command) as $value) {
                    if ($value instanceof AbstractCommand) {
                        $this->handleAttributes($value);
                    }
                }
                continue;
            }
            if ($this->isAbstractCommand($reflectionProperty)) {
                /** @var mixed $value */
                $value = $reflectionProperty->getValue($command);
                if ($value instanceof AbstractCommand) {
                    $this->handleAttributes($value);
                }
            }

            $this->handlePropertyAttribute($reflectionProperty, $command);
        }
    }

    private function handlePropertyAttribute(\ReflectionProperty $reflectionProperty, AbstractCommand $command): void
    {
        $attributes = $reflectionProperty->getAttributes(Present::class, \ReflectionAttribute::IS_INSTANCEOF);

        foreach ($attributes as $presentAttribute) {
            /** @var Present $attribute */
            $attribute = $presentAttribute->newInstance();
            $presenter = null;

            if ($this->container->has($attribute->presenter)) {
                /** @var PresenterInterface $presenter */
                $presenter = $this->container->get($attribute->presenter);
            }

            if (null === $presenter) {
                /** @var PresenterInterface $presenter */
                $presenter = new $attribute->presenter();
            }

            $sourceReflectionProperty = null !== $attribute->sourcePropertyName
                ? new \ReflectionProperty($reflectionProperty->class, $attribute->sourcePropertyName)
                : $reflectionProperty;

            $reflectionProperty->setValue(
                $command,
                $presenter->present($sourceReflectionProperty->getValue($command))
            );
        }
    }

    private function isAbstractCommand(\ReflectionProperty $reflectionProperty): bool
    {
        $type = (string) $reflectionProperty->getType();

        if (!class_exists($type)) {
            return false;
        }

        return is_subclass_of($type, AbstractCommand::class);
    }

    private function isArray(\ReflectionProperty $reflectionProperty): bool
    {
        $type = (string) $reflectionProperty->getType();

        return 'array' === $type;
    }
}