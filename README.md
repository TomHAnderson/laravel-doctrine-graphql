GraphQL for Doctrine using Hydrators for Laravel
================================================

[![Build Status](https://travis-ci.org/API-Skeletons/laravel-doctrine-graphql.svg)](https://travis-ci.org/API-Skeletons/laravel-doctrine-graphql)
[![Coverage](https://coveralls.io/repos/github/API-Skeletons/laravel-doctrine-graphql/badge.svg?branch=master&124)](https://coveralls.io/repos/github/API-Skeletons/laravel-doctrine-graphql/badge.svg?branch=master&124)
[![PHPStan](https://img.shields.io/badge/PHPStan-enabled-brightgreen.svg?style=flat)](https://github.com/phpstan/phpstan)
[![Documentation Status](https://readthedocs.org/projects/api-skeletons-laravel-doctrine-graphql/badge/?version=latest)](https://api-skeletons-laravel-doctrine-graphql.readthedocs.io/en/latest/?badge=latest)
[![PHP Version](https://img.shields.io/badge/PHP-8.0-blue)](https://img.shields.io/badge/PHP-8.0-blue)
[![Laravel Version](https://img.shields.io/badge/Laravel-8.x-red)](https://img.shields.io/badge/Laravel-8.x-red)
[![Gitter](https://badges.gitter.im/api-skeletons/open-source.svg)](https://gitter.im/api-skeletons/open-source)
[![Patreon](https://img.shields.io/badge/patreon-donate-yellow.svg)](https://www.patreon.com/apiskeletons)
[![Total Downloads](https://poser.pugx.org/api-skeletons/laravel-doctrine-graphql/downloads)](https://packagist.org/packages/api-skeletons/doctrine-graphql)
[![License](https://poser.pugx.org/api-skeletons/laravel-doctrine-hal/license)](//packagist.org/packages/api-skeletons/laravel-doctrine-graphql)


## This repository is a work in progress

This library uses Doctrine native traversal of related objects to provide full GraphQL
querying of entities and all related fields and entities.
Entity metadata is introspected and is therefore Doctrine data driver agnostic.
Data is collected with hydrators thereby
allowing full control over each field using hydrator filters, strategies and naming strategies.
Multiple object managers are supported. Multiple hydrator configurations are supported.
Works with [GraphiQL](https://github.com/graphql/graphiql).

[A range of filters](http://graphql.apiskeletons.com/en/latest/queries.html)
are provided to filter collections at any location in the query.

Doctrine provides easy taversal of your database.  Consider the following imaginary query:
```php
$entity[where id = 5]
  ->getRelation()
    ->getField1()
    ->getField2()
    ->getManyToOne([where name like '%dev%'])
      ->getName()
      ->getField3()
  ->getOtherRelation()
    ->getField4()
    ->getField5()
```

And see it realized in GraphQL with fine grained control over each field via hydrators:

```js
  { 
    entity (filter: { id: 5 }) { 
      relation { 
        field1 
        field2 
        manyToOne (filter: { name_contains: 'dev' }) { 
          name 
          field3 
        } 
      } otherRelation { 
        field4 
        field5 
      } 
    } 
  }
```


[Read the Documentation](https://apiskeletons-doctrine-graphql.readthedocs.io/en/latest/)
==========================================================

