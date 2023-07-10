<?php


use App\Kernel;
use Rector\Config\RectorConfig;
use Rector\Core\ValueObject\PhpVersion;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\Php71\Rector\FuncCall\CountOnNullRector;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Php81\Rector\ClassMethod\NewInInitializerRector;
use Rector\Renaming\Rector\Name\RenameClassRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Symfony\Set\SensiolabsSetList;
use Rector\Symfony\Set\SymfonyLevelSetList;
use Rector\Symfony\Set\SymfonySetList;

// @see https://github.com/rectorphp/rector/blob/main/docs/rector_rules_overview.md
return static function (RectorConfig $rectorConfig): void {
    $curDir = __DIR__ . \DIRECTORY_SEPARATOR;

    $rectorConfig->symfonyContainerXml($curDir . 'var/cache/dev/App_Framework_KernelDevDebugContainer.xml');
    $rectorConfig->bootstrapFiles([$curDir . 'vendor/autoload.php']);
    $rectorConfig->phpVersion(PhpVersion::PHP_81);
    $rectorConfig->phpstanConfig('phpstan.neon');

    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_81,
        SymfonyLevelSetList::UP_TO_SYMFONY_61,
        DoctrineSetList::ANNOTATIONS_TO_ATTRIBUTES,
        SymfonySetList::SYMFONY_CODE_QUALITY,
        SymfonySetList::ANNOTATIONS_TO_ATTRIBUTES,
        SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION,
    ]);

    $paths[] = $curDir . 'src';

    $rectorConfig->paths($paths);

    $rectorConfig->skip([
        $curDir . 'vendor',
        ClassPropertyAssignToConstructorPromotionRector::class,
        CountOnNullRector::class,
        NewInInitializerRector::class,
        RenameClassRector::class,
    ]);

    $rectorConfig->parallel();
};