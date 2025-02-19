Setting up FOSMessageBundle
===========================

### Step 1 - Requirements and Installing the bundle

> **Note** FOSMessageBundle by default has a requirement for FOSUserBundle for some of its
> interfaces which can be swapped out if you're not using FOSUserBundle. See
> [Using other UserBundles][] for more information.

The first step is to tell composer that you want to download FOSMessageBundle which can
be achieved by typing the following at the command prompt:

#### Current

```bash
composer require friendsofsymfony/message-bundle
```

#### v1.3 (Legacy: Symfony 2, or <=3.3)

```bash
composer require friendsofsymfony/message-bundle:1.3.0
```

### Step 2 - Setting up your user class

FOSMessageBundle requires that your user class implement `ParticipantInterface`. This
bundle does not have any direct dependencies to any particular UserBundle or
implementation of a user, except that it must implement the above interface.

Your user class may look something like the following:

```php
<?php
// src/Entity/User.php

use Doctrine\ORM\Mapping as ORM;
use FOS\MessageBundle\Model\ParticipantInterface;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * @ORM\Entity
 */
class User extends BaseUser implements ParticipantInterface
{
}
```

### Step 3 - Set up FOSMessageBundle's models

FOSMessageBundle has multiple models that must be implemented by you in an application
bundle (that may or may not be a child of FOSMessageBundle).

We provide examples for both Mongo DB and ORM.

- [Example entities for Doctrine ORM][]
- [Example documents for Doctrine ODM][]

### Step 4 - Enable the bundle in your kernel

The bundle must be added to your `bundles.php`.

**Step usually not necescary in Symfony 4**.

```php
// config/bundles.php

return [
    // ...
    FOS\MessageBundle\FOSMessageBundle::class => ['all' => true],
    // ...
];
```

### Step 5 - Import the bundle routing

Add FOSMessageBundle's routing to your application with an optional routing prefix.

```yaml
# config/routing.yaml

fos_message:
    resource: "@FOSMessageBundle/Resources/config/routing.xml"
    prefix: /optional_routing_prefix
```

### Step 6 - Check templating

Make sure to have twig installed and ready to be used


## Installation Finished

At this point, the bundle has been installed and configured and should be ready for use.
You can continue reading documentation by returning to the [index][].

[Example entities for Doctrine ORM]: 01a-orm-models.md
[Example documents for Doctrine ODM]: 01b-odm-models.md
[index]: 00-index.md
[Using other UserBundles]: 99-using-other-user-bundles.md
