Jose Bundle
===========

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Spomky-Labs/JoseBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Spomky-Labs/JoseBundle/?branch=master)
[![Build Status](https://travis-ci.org/Spomky-Labs/jose-bundle.svg?branch=master)](https://travis-ci.org/Spomky-Labs/jose-bundle)
[![HHVM Status](http://hhvm.h4cc.de/badge/Spomky-Labs/jose-bundle.png)](http://hhvm.h4cc.de/package/Spomky-Labs/jose-bundle)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/5398e4ca-1a48-4186-9410-f44f3f850a05/big.png)](https://insight.sensiolabs.com/projects/5398e4ca-1a48-4186-9410-f44f3f850a05)

[![Latest Stable Version](https://poser.pugx.org/Spomky-Labs/jose-bundle/v/stable.png)](https://packagist.org/packages/Spomky-Labs/jose-bundle)
[![Total Downloads](https://poser.pugx.org/Spomky-Labs/jose-bundle/downloads.png)](https://packagist.org/packages/Spomky-Labs/jose-bundle)
[![Latest Unstable Version](https://poser.pugx.org/Spomky-Labs/jose-bundle/v/unstable.png)](https://packagist.org/packages/Spomky-Labs/jose-bundle)
[![License](https://poser.pugx.org/Spomky-Labs/jose-bundle/license.png)](https://packagist.org/packages/Spomky-Labs/jose-bundle)

This Symfony2 bundle provides services create load and verify JWT.
It uses [Spomky-Labs/jose](https://github.com/Spomky-Labs/jose) to ease encryption/decryption and signature/verification of JWS and JWE.

# The Release Process
The release process [is described here](doc/Release.md).

# Prerequisites

This library needs at least:
* ![PHP 5.6+](https://img.shields.io/badge/PHP-5.6%2B-ff69b4.svg)
* Symfony 2.7+

Please consider the following optional third party libraries and extensions:
* Enable AES-GCM based algorithms (AxxxGCM and AxxxGCMKW): [PHP Crypto](https://github.com/bukka/php-crypto) Extension (not yet available on `PHP 7` and `HHVM`).
* Enable storage and `jti` header support: [doctrine/orm](https://packagist.org/packages/doctrine/orm) (should work with [doctrine/mongodb-odm](https://packagist.org/packages/doctrine/mongodb-odm) or other Doctrine storage).

# Continuous Integration

It has been successfully tested using `PHP 5.6`, `PHP 7` and `HHVM` with all algorithms under Symfony 2.7.

We also track bugs and code quality using [Scrutinizer-CI](https://scrutinizer-ci.com/g/Spomky-Labs/JoseBundle/) and [Sensio Insight](https://insight.sensiolabs.com/projects/5398e4ca-1a48-4186-9410-f44f3f850a05).

Coding Standards are verified by [StyleCI](https://styleci.io/repos/28856829).

# Installation

The preferred way to install this library is to rely on Composer:

```sh
composer require spomky-labs/jose-bundle "dev-master"
```

Then, add the bundle into your kernel:

```php
<?php

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            ...
            new SpomkyLabs\JoseBundle\SpomkyLabsJoseBundle(),
        ];

        return $bundles;
    }
}
```

# Configuration

This bundle needs to be configured. Please [see this page](Resources/doc/Configuration.md) to know how to configure it.

# Create your entities and managers



# How to use

Now that your bundle is enabled and configured, you are ready to create or load your first JWT.
Have a look at [this page](Resources/doc/Use.md) and lets do the magic.

# Contributing

Requests for new features, bug fixed and all other ideas to make this library useful are welcome. [Please follow these best practices](Resources/doc/Contributing.md).

# Licence

This software is release under [MIT licence](LICENSE).
