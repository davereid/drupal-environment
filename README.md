# Drupal Environment

[![CI](https://github.com/davereid/drupal-environment/actions/workflows/ci.yml/badge.svg)](https://github.com/davereid/drupal-environment/actions/workflows/ci.yml) [![Maintainability](https://api.codeclimate.com/v1/badges/a6cfe958e8316d8a4ac5/maintainability)](https://codeclimate.com/github/davereid/drupal-environment/maintainability) [![Test Coverage](https://api.codeclimate.com/v1/badges/a6cfe958e8316d8a4ac5/test_coverage)](https://codeclimate.com/github/davereid/drupal-environment/test_coverage) [![Packagist](https://img.shields.io/packagist/dt/davereid/drupal-environment.svg)](https://packagist.org/packages/davereid/drupal-environment)

Provides a class for working with Drupal environments and environment variables.

This also standardizes some environment terminology between hosting providers so that you can use the same code across different hosts:

| Environment | Acquia | Pantheon |
|-|-|-|
| Production | `prod` | `live` |
| Staging | `test` | `test` |
| Development | `dev` | `dev` |

## Basic Usage

### Getting an environment variable

```php
use DrupalEnvironment\Environment;
$value = Environment::get('VARIABLE_NAME');
```

The advantages of using this is the results are statically cached.

### Testing for Drupal hosting or CI environments

```php
use DrupalEnvironment\Environment;

// These all return a boolean true/false
Environment::isPantheon();
Environment::isAcquia();
Environment::isTugboat();
Environment::isGitHubWorkflow();
Environment::isGitLabCi();
Environment::isCircleCi();
```

### Testing for specific environments

```php
use DrupalEnvironment\Environment;

// This gets the specific environment string.
$environment = Environment::getEnvironment();

// These all return a boolean true/false
Environment::isProduction();
Environment::isStaging();
Environment::isDevelopment();
Environment::isCi();
Environment::isLocal(); // Covers both DDEV and Lando
Environment::isDdev();
Environment::isLando();
```

### Testing for executable commands

```php
use DrupalEnvironment\Environment;

// This returns a boolean true/false:
Environment::commandExists('composer');
```

## Example usage

### settings.php

```php
use DrupalEnvironment\Environment;

if (Environment::isProduction()) {
  // Set some production environment settings overrides.
}
elseif (Environment::isStaging()) {
  // Set some staging environment settings overrides.
}
elseif (Environment::isDevelopment()) {
  // Set some development environment settings overrides.
}
elseif (Environment::isLocal()) {
  // Set some development environment settings overrides.
}

// Include a environment-specific settings file.
if ($environment = Environment::getEnvironment()) {
  $settings_file = 'settings.' . $environment . '.php';
  if (is_file($settings_file)) {
    require_once $settings_file;
  }
}
```
