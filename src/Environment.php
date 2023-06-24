<?php

namespace Drupal\helper\Environment;

/**
 * Helpers for working with the environment.
 *
 * @todo Add Platform.sh support
 *
 * This class should be usable outside of Drupal.
 *
 * @method static bool isAcquia()
 * @method static bool isPantheon()
 * @method static bool isDefault()
 * @method static string getEnvironment()
 * @method static bool isPreview()
 */
class Environment {

  /**
   * The currently supported environment classes.
   */
  public const CLASSES = [
    'isAcquia' => AcquiaEnvironment::class,
    'isPantheon' => PantheonEnvironment::class,
    'isDefault' => DefaultEnvironment::class,
  ];

  /**
   * Provide a shortcut for calling methods on the environment classes.
   */
  public static function __callStatic($name, $arguments) {
    $class = static::getEnvironmentClass();

    // Provide special case for methods like isPantheon() or isAcquia().
    if (isset(static::CLASSES[$name])) {
      return $class === static::CLASSES[$name];
    }

    return $class::$name(...$arguments);
  }

  /**
   * Determine which environment class to use.
   *
   * @return string
   *   The class name.
   */
  public static function getEnvironmentClass() {
    static $class;
    if (!isset($class)) {
      // Intentionally re-assigning the class variable here so that a match
      // breaks the foreach loop, or we fall back to the default class.
      foreach (static::CLASSES as $class) {
        if (static::get($class::ENVIRONMENT_NAME)) {
          break;
        }
      }
    }
    return $class;
  }

  /**
   * Get an environment variable.
   *
   * @param string $name
   *   The name of the environment variable to retrieve.
   *
   * @return mixed
   *   The environment variable, if it's set.
   */
  public static function get(string $name): mixed {
    // @todo Should this have static caching?
    return getenv($name);
  }

  /**
   * Determine if this is a CI environment.
   *
   * @return bool
   *   TRUE if this is CI.
   */
  public static function isCi(): bool {
    return static::getEnvironment() === 'ci';
  }

  /**
   * Determine if this is a local environment.
   *
   * @todo Add Lando support.
   *
   * @return bool
   *   TRUE if this is a local environment.
   */
  public static function isLocal(): bool {
    return static::getEnvironment() === 'local' || static::isDdev();
  }

  /**
   * Determine if this is a DDEV environment.
   *
   * @return bool
   *   TRUE if this is a DDEV environment.
   */
  public static function isDdev(): bool {
    return (bool) static::get('IS_DDEV_PROJECT');
  }

  /**
   * Determine if this is a Tugboat environment.
   *
   * @return bool
   *   TRUE if this is a Tugboat environment.
   */
  public static function isTugboat(): bool {
    return static::getEnvironment() === 'tugboat';
  }

  /**
   * Determines whether the current request is a command-line one.
   *
   * @return bool
   *   TRUE if this request was originated in command-line (cli), FALSE
   *   otherwise.
   */
  public static function isCli() {
    return (PHP_SAPI === 'cli');
  }

  /**
   * Tests if a command exists on the server.
   *
   * @param string $command
   *   The command to test for.
   *
   * @return bool
   *   TRUE if the command exists, or FALSE otherwise.
   */
  public static function commandExists(string $command): bool {
    $command = escapeshellcmd($command);
    return (bool) shell_exec("command -v {$command}");
  }

  /**
   * Get the actual filename for composer.json.
   *
   * @return string
   *   The composer.json filename.
   */
  public static function getComposerFilename(): string {
    return static::get('COMPOSER') ?: 'composer.json';
  }

  /**
   * Get the actual filename for composer.lock.
   *
   * @return string
   *   The composer.lock filename.
   */
  public static function getComposerLockFilename(): string {
    $composer_filename = static::getComposerFilename();
    return pathinfo($composer_filename, PATHINFO_FILENAME) . '.lock';
  }

}
