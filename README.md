# Celtic34fr Contact Extension v0.0.1-dev

Author: Gilbert ARMENGAUD

This Bolt extension provide a Contact Form and all the method to start a CRM module.
This module give the base functionalities and need other module to give all its power. The first one is
celtic34fr/contact-gestion that give real contact formular et anwser system management.

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
