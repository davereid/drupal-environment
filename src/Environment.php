<?php
declare(strict_types=1);

namespace DrupalEnvironment;

/**
 * Helpers for working with the Drupal environment.
 *
 * @method static string getEnvironment()
 * @method static bool isAcquia()
 * @method static bool isPantheon()
 * @method static bool isProduction()
 * @method static bool isStaging()
 * @method static bool isDevelopment()
 * @method static bool isPreview()
 * @method static bool isTugboat()
 * @method static bool isCi()
 * @method static bool isGitHubWorkflow()
 * @method static bool isGitLabCi()
 * @method static bool isCircleCi()
 * @method static array|null getIndicatorConfig()
 */
class Environment
{

    /**
     * The currently supported environment classes.
     */
    public const CLASSES = [
        'isAcquia' => Acquia::class,
        'isPantheon' => Pantheon::class,
        'isTugboat' => Tugboat::class,
        'isGitHubWorkflow' => GitHubWorkflow::class,
        'isGitLabCi' => GitLabCi::class,
        'isCircleCi' => CircleCi::class,
        null => DefaultEnvironment::class,
    ];

    /**
     * Determine which environment class to use.
     *
     * @return string
     *   The class name.
     */
    public static function getEnvironmentClass(): string
    {
        static $class;
        if (!isset($class)) {
            if ($class = static::get('DRUPAL_ENVIRONMENT_CLASS')) {
                // Do nothing. The class was assigned in the if.
            } else {
                // Intentionally re-assigning the class variable here so that a match
                // breaks the foreach loop, or we fall back to the default class.
                foreach (static::CLASSES as $class) {
                    if (static::get($class::ENVIRONMENT_NAME)) {
                        break;
                    }
                }
            }
        }
        return $class;
    }

    /**
     * Provide a shortcut for calling methods on the environment classes.
     */
    public static function __callStatic(string $name, array $arguments)
    {
        $class = static::getEnvironmentClass();

        // Provide special case for methods like isPantheon() or isAcquia().
        if (isset(static::CLASSES[$name])) {
            return $class === static::CLASSES[$name];
        }

        return $class::$name(...$arguments);
    }

    /**
     * Get an environment variable.
     *
     * @param string $name
     *   The name of the environment variable to retrieve.
     *
     * @return string|bool
     *   The environment variable, if it's set.
     */
    public static function get(string $name): string|bool
    {
        static $cache = [];
        if (!array_key_exists($name, $cache)) {
            $cache[$name] = getenv($name);
        }
        return $cache[$name];
    }

    /**
     * Determine if this is a local environment.
     *
     * @return bool
     *   TRUE if this is a local environment.
     */
    public static function isLocal(): bool
    {
        return static::getEnvironment() === 'local' || static::isDdev() || static::isLando();
    }

    /**
     * Determine if this is a DDEV environment.
     *
     * @return bool
     *   TRUE if this is a DDEV environment.
     *
     * @see https://ddev.readthedocs.io/en/latest/users/extend/custom-commands/#environment-variables-provided
     */
    public static function isDdev(): bool
    {
        return (bool)static::get('IS_DDEV_PROJECT');
    }

    /**
     * Determine if this is a Localdev or Lando environment.
     *
     * @return bool
     *   TRUE if this is a Localdev or Lando environment.
     *
     * @see https://docs.lando.dev/core/v3/env.html
     */
    public static function isLando(): bool
    {
        return (bool)static::get('LANDO_INFO');
    }

    /**
     * Determines whether the current request is a command-line one.
     *
     * @return bool
     *   TRUE if this request was originated in command-line (cli), FALSE
     *   otherwise.
     */
    public static function isCli(): bool
    {
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
    public static function commandExists(string $command): bool
    {
        $command = escapeshellcmd($command);
        return (bool)shell_exec("command -v {$command}");
    }

    /**
     * Get the actual filename for composer.json.
     *
     * @return string
     *   The composer.json filename.
     */
    public static function getComposerFilename(): string
    {
        return static::get('COMPOSER') ?: 'composer.json';
    }

    /**
     * Get the actual filename for composer.lock.
     *
     * @return string
     *   The composer.lock filename.
     */
    public static function getComposerLockFilename(): string
    {
        $filename = static::getComposerFilename();
        return pathinfo($filename, PATHINFO_FILENAME) . '.lock';
    }

    /**
     * Get the current host name.
     *
     * @return string
     *   The current host name.
     */
    public static function getHost(): string
    {
        static $host;
        if (!isset($host)) {
            $possibleHostSources = array('HTTP_X_FORWARDED_HOST', 'HTTP_HOST', 'SERVER_NAME', 'SERVER_ADDR');
            $sourceTransformations = array(
                "HTTP_X_FORWARDED_HOST" => function ($value) {
                    $elements = explode(',', $value);
                    return trim(end($elements));
                }
            );
            $host = '';
            foreach ($possibleHostSources as $source) {
                if (!empty($host)) {
                    break;
                }
                if (empty($_SERVER[$source])) {
                    continue;
                }
                $host = $_SERVER[$source];
                if (array_key_exists($source, $sourceTransformations)) {
                    $host = $sourceTransformations[$source]($host);
                }
            }

            // trim and remove port number from host
            // host is lowercase as per RFC 952/2181
            $host = strtolower(preg_replace('/:\d+$/', '', trim($host)));
        }
        return $host;
    }

    /**
     * Redirect requests to a preferred domain.
     *
     * This will not redirect CLI requests.
     *
     * @param string $domain
     *   The preferred domain name.
     */
    public static function enforceDomain(string $domain): void
    {
        if (!static::isCli() && static::getHost() !== $domain) {
            // Name transaction "redirect" in New Relic for improved reporting.
            if (extension_loaded('newrelic')) {
                newrelic_name_transaction('redirect');
            }

            header('HTTP/1.0 301 Moved Permanently');
            header('Location: https://' . $domain . $_SERVER['REQUEST_URI']);
            exit();
        }
    }
}
