# Drupal Environment

[![CI](https://github.com/davereid/drupal-environment/actions/workflows/ci.yml/badge.svg)](https://github.com/davereid/drupal-environment/actions/workflows/ci.yml) [![Maintainability](https://api.codeclimate.com/v1/badges/a6cfe958e8316d8a4ac5/maintainability)](https://codeclimate.com/github/davereid/drupal-environment/maintainability) [![Test Coverage](https://api.codeclimate.com/v1/badges/a6cfe958e8316d8a4ac5/test_coverage)](https://codeclimate.com/github/davereid/drupal-environment/test_coverage) [![Packagist](https://img.shields.io/packagist/dt/davereid/drupal-environment.svg)](https://packagist.org/packages/davereid/drupal-environment)

Provides a class for working with Drupal environments and environment variables.

## Basic Usage

### Getting an environment variable

```php
use Davereid\DrupalEnvironment\Environment;
$value = Environment::get('VARIABLE_NAME');
```

The advantages of using this is the results are statically cached.

### Testing for Drupal hosting or CI environments

```php
use Davereid\DrupalEnvironment\Environment;

$result = Environment::isPantheon();
$result = Environment::isAcquia();
$result = Environment::isTugboat();
$result = Environment::isCi();

```

