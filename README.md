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
Environment::isGenericCi(); // Any other CI service that sets CI=true
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

## Known Issues

### CI isn't detected inside DDEV

DDEV doesn't pass the CI system's environment variables, such as `CI` or `GITHUB_WORKFLOW`, into its containers. Code run in a CI job with `ddev exec` or `ddev composer` sees a local DDEV environment: `Environment::isCi()` and the CI methods, such as `Environment::isGitHubWorkflow()`, return `FALSE`. [ddev/ddev#9124](https://github.com/ddev/ddev/pull/9124) will pass the host's `CI` variable into DDEV's web container, which is enough for `Environment::isCi()`.

Until then, pass `CI` into DDEV yourself. Also pass the CI system's own variable if your code calls its method, such as `GITHUB_WORKFLOW` for `Environment::isGitHubWorkflow()`, `GITLAB_CI` for `Environment::isGitLabCi()` or `CIRCLECI` for `Environment::isCircleCi()`. `Environment::isLocal()` and `Environment::isDdev()` still return `TRUE`. Either:

- List the variables by name in `.ddev/config.yaml`. DDEV passes each one through from the host when it's set there, so nothing changes on developers' machines:
  ```yaml
  web_environment:
    - CI
    - GITHUB_WORKFLOW
  ```
- Or, in a GitHub Actions workflow, write them after `ddev/github-action-setup-ddev` with `autostart: false` and before `ddev start`. DDEV 1.25.4 and later read `.ddev/.env.web.local`, which is gitignored, so the checkout has no uncommitted changes:
  ```yaml
  - run: ddev dotenv set .ddev/.env.web.local --ci=true --github-workflow="$GITHUB_WORKFLOW"
  ```

## Recommended Projects

### Loading .env files

- https://github.com/symfony/dotenv
- https://github.com/vlucas/phpdotenv
