# Drupal Environment

[![CI](https://github.com/davereid/drupal-environment/actions/workflows/ci.yml/badge.svg)](https://github.com/davereid/drupal-environment/actions/workflows/ci.yml) [![Maintainability](https://qlty.sh/gh/davereid/projects/drupal-environment/maintainability.svg)](https://qlty.sh/gh/davereid/projects/drupal-environment) [![Code Coverage](https://qlty.sh/gh/davereid/projects/drupal-environment/coverage.svg)](https://qlty.sh/gh/davereid/projects/drupal-environment) [![Packagist](https://img.shields.io/packagist/dt/davereid/drupal-environment.svg)](https://packagist.org/packages/davereid/drupal-environment)

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
Environment::isTest(); // Covers PHPUnit and Playwright tests
```

### Testing for executable commands

```php
use DrupalEnvironment\Environment;

// This returns a boolean true/false:
Environment::commandExists('composer');
```

### Getting the PHP memory limit

```php
use DrupalEnvironment\Environment;

// These return bytes, or -1 if memory is unlimited:
Environment::getMemoryLimit();
Environment::getAvailableMemory();
```

## Example usage

### settings.php

Include this package's [settings file](settings/settings.environment.php) from your site's `settings.php`:

```php
include \DrupalEnvironment\Environment::settingsFile();
```

It configures the [Environment Indicator](https://www.drupal.org/project/environment_indicator) module for the current environment. It also includes these settings files next to `settings.php`, in this order, when they exist:

- `settings.<environment>.php` for the current environment, like `settings.prod.php` or `settings.dev.php`.
- `settings.ci.php` when `Environment::isCi()` is `TRUE`.
- `settings.testing.php` when `Environment::isTest()` is `TRUE`, for PHPUnit and Playwright runs. It isn't `settings.test.php`, which is the staging environment's file on Acquia and Pantheon.

For other per-environment settings:

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
elseif (Environment::isCi()) {
  // Set some CI environment settings overrides.
}
elseif (Environment::isTest()) {
  // Set some PHPUnit or Playwright test settings overrides.
}
elseif (Environment::isLocal()) {
  // Set some local environment settings overrides.
}

// Redirect any internal platform domains to a preferred domain.
if (Environment::isProduction()) {
  Environment::enforceDomain('www.example.com');
}
```

## Recommended Projects

### Loading .env files

- https://github.com/symfony/dotenv
- https://github.com/vlucas/phpdotenv
