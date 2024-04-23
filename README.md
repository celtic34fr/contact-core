# Bolt CMS 5 Celtic34fr Contact Extension Core v0.0.1-dev

Author: Gilbert ARMENGAUD

This Bolt extension provide a Contact Form and all the method to start a CRM module.
This module give the base functionalities and need other module to give all its power. The first one is
celtic34fr/contact-gestion that give real contact formular et anwser system management.

## Requierement:
Create a Bolt CMS project according the official documentation. 

Then install the make-bundle from Symfony
```bash
composer require --dev symfony/maker-bundle
```
If the current version is not published as stable, change the stability requirement in the composer.json file to 'dev'

## Installation:

```bash
composer require celtic34fr/contact-core
```
If composer return some errors that notice to you that the module cannot be install, use the following command :
```bash
composer -W require celtic34fr/contact-core
```

After, if you use Doctrine ORM, you must update your database with the following command :
```bash
symfony console make:migration
```
And after :
```bash
composer console doctrine:migrations:migrate
```
Now the installation is quiet finish.

## Running PHPStan and Easy Codings Standard

First, make sure dependencies are installed:

```
COMPOSER_MEMORY_LIMIT=-1 composer update
```

And then run ECS:

```
vendor/bin/ecs check src
```
