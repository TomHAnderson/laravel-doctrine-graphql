<?php

namespace ZF\Doctrine\GraphQL\Field;

use Interop\Container\ContainerInterface;

class FieldResolverFactory
{
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array $options = null
    ) {
        $hydratorManager = $container->get('HydratorManager');
        $config = $container->get('config');

        return new FieldResolver($hydratorManager, $config);
    }
}