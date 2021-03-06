<?php

namespace ApiSkeletons\Laravel\Doctrine\GraphQL\Command;

use Exception;
use Interop\Container\ContainerInterface;
use Laminas\Mvc\Console\Controller\AbstractConsoleController;
use Laminas\Config\Config;
use Laminas\Config\Writer\PhpArray;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use ApiSkeletons\Laravel\Doctrine\GraphQL\Hydrator\Strategy;
use ApiSkeletons\Laravel\Doctrine\GraphQL\Hydrator\Filter;


use Illuminate\Console\Command;
use Doctrine\Common\Persistence\ManagerRegistry;

final class ConfigurationSkeletonCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'doctrine:graphql:configuration-skeleton
        {--hydrator-section=*} {--entity-manager=default}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-generating configuration tool";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(ManagerRegistry $managerRegistry)
    {
        $entityManager = $managerRegistry->getManager($this->option('entity-manager'));

        $metadata = $entityManager->getMetadataFactory()->getAllMetadata();
        usort($metadata, function ($a, $b) {
            return $a->getName() <=> $b->getName();
        });

        $config = [];

        $hydratorSections = $this->option('hydrator-section') ?? ['default'];

        foreach ($hydratorSections as $section) {
            foreach ($metadata as $classMetadata) {
                $hydratorAlias = 'ApiSkeletons\\Laravel\\Doctrine\\GraphQL\\Hydrator\\'
                    . str_replace('\\', '_', $classMetadata->getName());

                $strategies = [];
                $filters = [];
                $documentation = ['_entity' => ''];

                // Sort field names
                $fieldNames = $classMetadata->getFieldNames();
                usort($fieldNames, function ($a, $b) {
                    if ($a == 'id') {
                        return -1;
                    }

                    if ($b == 'id') {
                        return 1;
                    }

                    return $a <=> $b;
                });

                foreach ($fieldNames as $fieldName) {
                    $documentation[$fieldName] = '';
                    $fieldMetadata = $classMetadata->getFieldMapping($fieldName);

                    // Handle special named fields
                    if ($fieldName == 'password' || $fieldName == 'secret') {
                        $filters['password'] = [
                            'condition' => 'and',
                            'filter' => Filter\Password::class,
                        ];
                        continue;
                    }

                    // Handle all other fields
                    switch ($fieldMetadata['type']) {
                        case 'tinyint':
                        case 'smallint':
                        case 'integer':
                        case 'int':
                        case 'bigint':
                            $strategies[$fieldName] = Strategy\ToInteger::class;
                            break;
                        case 'boolean':
                            $strategies[$fieldName] = Strategy\ToBoolean::class;
                            break;
                        case 'decimal':
                        case 'float':
                            $strategies[$fieldName] = Strategy\ToFloat::class;
                            break;
                        case 'string':
                        case 'text':
                        case 'datetime':
                        default:
                            $strategies[$fieldName] = Strategy\FieldDefault::class;
                            break;
                    }
                }

                // Sort association Names
                $associationNames = $classMetadata->getAssociationNames();
                usort($associationNames, function ($a, $b) {
                    return $a <=> $b;
                });

                foreach ($associationNames as $associationName) {
#                    $documentation[$associationName] = '';
                    $mapping = $classMetadata->getAssociationMapping($associationName);

                    // See comment on NullifyOwningAssociation for details of why this is done
                    if ($mapping['type'] == ClassMetadataInfo::MANY_TO_MANY && $mapping['isOwningSide']) {
                        $strategies[$associationName] = Strategy\NullifyOwningAssociation::class;
                    } else {
                        $strategies[$associationName] = Strategy\AssociationDefault::class;
                    }
                }

                $filters['default'] = [
                    'condition' => 'and',
                    'filter' => Filter\FilterDefault::class,
                ];

                $config[$hydratorAlias][$section] = [
                    'entity_class' => $classMetadata->getName(),
                    'object_manager' => $objectManagerAlias,
                    'by_value' => true,
                    'use_generated_hydrator' => true,
                    'hydrator' => null,
                    'naming_strategy' => null,
                    'strategies' => $strategies,
                    'filters' => $filters,
                    'documentation' => $documentation,
                ];
            }
        }

        $configObject = new Config($config);
        $writer = new PhpArray();
        $writer->setUseBracketArraySyntax(true);
        $writer->setUseClassNameScalars(true);

        echo $writer->toString($configObject);

        $this->info('Configuration complete");

        return 0;
    }
}
