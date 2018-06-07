<?php

namespace ZF\Doctrine\GraphQL\Hydrator\Strategy;

use Zend\Hydrator\Strategy\StrategyInterface;
use DoctrineModule\Stdlib\Hydrator\Strategy\AbstractCollectionStrategy;
use ZF\Hal\Collection;

/**
 * Transform a number value into a php native float
 *
 * @returns float
 */
class ToFloat extends AbstractCollectionStrategy implements
    StrategyInterface
{
    public function extract($value)
    {
        return floatval($value);
    }

    public function hydrate($value)
    {
        return floatval($value);
    }
}
