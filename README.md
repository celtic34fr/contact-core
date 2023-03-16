# Celtic34fr Contact Extension

Author: Gilbert ARMENGAUD

This Bolt extension provide a Contact Form and all the method to start a CRM module.

Installation:

```bash
composer require celtic34fr/contact-core
```

In your project, folder config/packages, in doctrine.yaml file, you have to add the following libes in doctrine/orm section:
```
        dql:
            string_functions:
                MATCH_AGAINST: Celtic34fr\ContactCore\Extensions\Doctrine\MatchAgainst
```
for integrating fulltext search needed by other CRM Extension as CRM-Contact

## Running PHPStan and Easy Codings Standard

First, make sure dependencies are installed:

```
COMPOSER_MEMORY_LIMIT=-1 composer update
```

And then run ECS:

```
vendor/bin/ecs check src
```
