TalavPermissionBundle
=============

The TalavPermissionBundle adds support for user avatar. Current version only supports integration with TalavMediaBundle and TalavUserBundle

Installation
============

Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Applications that use Symfony Flex
----------------------------------

Open a command console, enter your project directory and execute:

```console
$ composer require talav/avatar-bundle
```

Applications that don't use Symfony Flex
----------------------------------------

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require talav/avatar-bundle
```

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    Talav\Bundle\MediaBundle\TalavPermissionBundle:class             => ['all' => true]
];
```

Configuration
============

### Step 1: Configure `TalavUserBundle` and `TalavMediaBundle`

### Step 2: Add avatar field to user entity
Symfony [suggests](https://symfony.com/doc/current/best_practices.html#use-attributes-to-define-the-doctrine-entity-mapping) to use PHP attributes to define the Doctrine Entity Mapping
Media entity:

```php

// src/Entity/User.php

<?php

use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Talav\PermissionBundle\Model\UserAvatarInterface;
use Talav\Component\Media\Model\MediaInterface;

// Make sure User class implements UserAvatarInterface
class User extends BaseUser implements UserAvatarInterface
{
    #[OneToOne(targetEntity: "Talav\Component\Media\Model\MediaInterface", cascade: ['persist'])]
    #[JoinColumn(name: 'media_id')]
    protected ?MediaInterface $avatar = null;

    public function getAvatar(): ?MediaInterface
    {
        return $this->avatar;
    }

    public function setAvatar(?MediaInterface $avatar): void
    {
        $this->avatar = $avatar;
    }
}
```

### Step 3: Enable routes in `config/routes/talav.yaml`
```yaml
talav_user:
  resource: '@TalavPermissionBundle/Resources/config/routing.yml'
```
