# Imperium Clan Software - Mailing Bundle

Symfony bundle for extend security and logging

## Installation


Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Applications that use Symfony Flex

Open a command console, enter your project directory and execute:

```console
composer require ics/mailing-bundle
```

### Applications that don't use Symfony Flex

#### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require ics/mailing-bundle
```

#### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    ICS\MailingBundle\MailingBundle::class => ['all' => true],
];
```

#### Step 3: Adding bundle routing

Add routes in applications `config/routes.yaml`

```yaml
# config/routes.yaml

# ...
Mailing_bundle:
    resource: '@MailingBundle/config/routes.yaml'
    prefix: /mailing
# ...
```

#### Step 4: Install Database

For install database :

```bash
# Installer la base de données

php bin/console doctrine:schema:create

```

For update database :

```bash
# Mise a jour la base de données

php bin/console doctrine:schema:update -f

```

