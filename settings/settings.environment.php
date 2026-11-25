<?php

/**
 * @file
 * Environment settings for a Drupal site.
 *
 * Include this file from the site's settings.php:
 * @code
 * include \DrupalEnvironment\Environment::settingsFile();
 * @endcode
 */

declare(strict_types=1);

use DrupalEnvironment\Environment;

// Add configuration for the Environment Indicator module.
$config['environment_indicator.indicator'] = Environment::getIndicatorConfig();

// Include environment-specific settings files next to settings.php, when they
// exist. Drupal sets $app_root and $site_path for settings.php. These use
// require, not require_once, since Drupal can load settings.php more than once
// in a request, such as during installation.
if (isset($app_root, $site_path)) {
    $settings_dir = $app_root . '/' . $site_path;

    // For example, settings.prod.php or settings.dev.php.
    $environment = Environment::getEnvironment();
    if ($environment && is_file($settings_dir . '/settings.' . $environment . '.php')) {
        require $settings_dir . '/settings.' . $environment . '.php';
    }

    // CI jobs, unless the environment's own file was already settings.ci.php.
    if ($environment !== 'ci' && Environment::isCi() && is_file($settings_dir . '/settings.ci.php')) {
        require $settings_dir . '/settings.ci.php';
    }

    // PHPUnit and Playwright test runs. Not settings.test.php, which is the
    // staging environment's file on Acquia and Pantheon.
    if (Environment::isTest() && is_file($settings_dir . '/settings.testing.php')) {
        require $settings_dir . '/settings.testing.php';
    }
}
