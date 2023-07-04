<?php

declare(strict_types=1);

namespace App\Doctrine\Command\Provider;

use App\Doctrine\Command\Tools\SchemaTool;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\Provider\Exception\NoMappingFound;
use Doctrine\Migrations\Provider\SchemaProvider;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;

class OrmSchemaProvider  implements SchemaProvider
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function createSchema(): Schema
    {
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();

        if (0 === \count($metadata)) {
            throw NoMappingFound::new();
        }

        usort($metadata, static fn (ClassMetadata $a, ClassMetadata $b) => $a->getTableName() <=> $b->getTableName());

        $tool = new SchemaTool($this->entityManager);

        return $tool->getSchemaFromMetadata($metadata);
    }

    public function __invoke(): self
    {
        return $this;
    }
}