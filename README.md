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

### Testing for Drupal hosting environments

```php
use DrupalEnvironment\Environment;

// These all return a boolean true/false
Environment::isPantheon();
Environment::isAcquia();
Environment::isTugboat();
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
Environment::isCi(); // See "CI services" below
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

```php
use DrupalEnvironment\Environment;

// Add configuration for the Environment Indicator module.
$config['environment_indicator.indicator'] = Environment::getIndicatorConfig();

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

// Redirect any internal platform domains to a preferred domain.
if (Environment::isProduction()) {
  Environment::enforceDomain('www.example.com');
}

// Include a environment-specific settings file.
if ($environment = Environment::getEnvironment()) {
  $settings_file = 'settings.' . $environment . '.php';
  if (is_file($settings_file)) {
    require_once $settings_file;
  }
}
```

## CI services

`Environment::isCi()` returns `TRUE` when the `CI` variable is set to anything but a false-like value (`false`, `0`, `no` or `off`). These CI services set it:

| CI service | `CI` value |
|-|-|
| [AppVeyor](https://www.appveyor.com/docs/environment-variables/) | `True` |
| [Bitbucket Pipelines](https://support.atlassian.com/bitbucket-cloud/docs/variables-and-secrets/) | `true` |
| [Buildkite](https://buildkite.com/docs/pipelines/configure/environment-variables) | `true` |
| [CircleCI](https://circleci.com/docs/reference/variables/) | `true` |
| [Codefresh](https://codefresh.io/docs/docs/pipelines/variables/) | `true` |
| [Drone](https://docs.drone.io/pipeline/environment/reference/ci/) | `true` |
| [GitHub Actions](https://docs.github.com/en/actions/reference/workflows-and-actions/variables#default-environment-variables) | `true` |
| [GitLab CI/CD](https://docs.gitlab.com/ci/variables/predefined_variables/) | `true` |
| [Semaphore](https://docs.semaphore.io/reference/env-vars) | `true` |
| [Travis CI](https://docs.travis-ci.com/user/environment-variables/#default-environment-variables) | `true` |
| [Woodpecker](https://woodpecker-ci.org/docs/usage/environment) | `woodpecker` |

[Azure Pipelines](https://learn.microsoft.com/en-us/azure/devops/pipelines/build/variables), [Jenkins](https://www.jenkins.io/doc/book/pipeline/jenkinsfile/#using-environment-variables), [TeamCity](https://www.jetbrains.com/help/teamcity/predefined-build-parameters.html) and [AWS CodeBuild](https://docs.aws.amazon.com/codebuild/latest/userguide/build-env-ref-env-vars.html) don't set `CI`. For these and any other service that doesn't, add `CI=true` to the pipeline's environment variables.

### CI inside DDEV

DDEV 1.25.6 and later [pass the host's `CI` variable into the web container](https://docs.ddev.com/en/stable/users/configuration/environment-variables/#ci-from-the-host), so code run in a CI job with `ddev exec` or `ddev composer` gets `TRUE` from both `Environment::isCi()` and `Environment::isLocal()`.

With older DDEV versions, list `CI` by name in `.ddev/config.yaml`. DDEV passes it through from the host only when it's set there, so nothing changes on developers' machines:

```yaml
web_environment:
  - CI
```

## Recommended Projects

### Loading .env files

- https://github.com/symfony/dotenv
- https://github.com/vlucas/phpdotenv
