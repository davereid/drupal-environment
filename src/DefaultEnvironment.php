<?php
declare(strict_types=1);

namespace DrupalEnvironment;

/**
 * The standard environment.
 *
 * @internal
 */
class DefaultEnvironment
{

    /**
     * The default environment variable name.
     *
     * @var string
     */
    public const ENVIRONMENT_NAME = 'ENVIRONMENT';

    /**
     * The default production environment name.
     *
     * @var string
     */
    public const PRODUCTION = 'prod';

    /**
     * The default staging environment name.
     *
     * @var string
     */
    public const STAGING = 'test';

    /**
     * The default development environment name.
     *
     * @var string
     */
    public const DEVELOPMENT = 'dev';

    /**
     * The default preview environment name.
     *
     * @var string
     */
    public const PREVIEW = 'preview';

    /**
     * The default CI environment name.
     *
     * @var string
     */
    public const CI = 'ci';

    /**
     * Get an environment variable.
     *
     * @param string $name
     *   The name of the environment variable to retrieve.
     *
     * @return mixed
     *   The environment variable, if it's set.
     */
    public static function get(string $name)
    {
        return Environment::get($name);
    }

    /**
     * Return the environment name.
     *
     * For example: "local" or "ci" or "dev" or "prod".
     *
     * @return string|bool
     *   The name of the environment.
     */
    public static function getEnvironment(): string|bool
    {
        return static::get(static::ENVIRONMENT_NAME);
    }

    /**
     * Determine if this is a production environment.
     *
     * @return bool
     *   TRUE if this is a production environment.
     */
    public static function isProduction(): bool
    {
        return static::getEnvironment() === static::PRODUCTION;
    }

    /**
     * Determine if this is a staging environment.
     *
     * @return bool
     *   TRUE if this is a staging environment.
     */
    public static function isStaging(): bool
    {
        return static::getEnvironment() === static::STAGING;
    }

    /**
     * Determine if this is a development/test environment.
     *
     * @return bool
     *   TRUE if this is a development environment.
     */
    public static function isDevelopment(): bool
    {
        return static::getEnvironment() === static::DEVELOPMENT;
    }

    /**
     * Determine if this is a preview environment.
     *
     * @return bool
     *   TRUE if this is a preview environment.
     */
    public static function isPreview(): bool
    {
        return static::getEnvironment() === static::PREVIEW;
    }

    /**
     * Determine if this is a CI environment.
     *
     * @return bool
     *   TRUE if this is CI.
     */
    public static function isCi(): bool
    {
        return static::getEnvironment() === static::CI;
    }

    /**
     * Determine if this is a local environment.
     *
     * @return bool
     *   TRUE if this is local.
     */
    public static function isLocal(): bool
    {
        return Environment::isLocal();
    }

    /**
     * Get the environment_indicator configuration. for this environment.
     *
     * @return array|null
     *   The environment_indicator configuration or NULL if one could not be provided.
     *
     * @see https://architecture.lullabot.com/adr/20210609-environment-indicator/
     */
    public static function getIndicatorConfig(): ?array
    {
        if (static::isProduction()) {
            return [
                'name' => 'Production',
                'bg_color' => '#e7131a',
                'fg_color' => '#ffffff',
            ];
        }
        if (static::isStaging()) {
            return [
                'name' => 'Staging',
                'bg_color' => '#b85c00',
                'fg_color' => '#ffffff',
            ];
        }
        if (static::isDevelopment()) {
            return [
                'name' => 'Development',
                'bg_color' => '#307b24',
                'fg_color' => '#ffffff',
            ];
        }
        if (static::isPreview()) {
            return [
                'name' => 'Preview',
                'bg_color' => '#20688C',
                'fg_color' => '#ffffff',
            ];
        }
        if (static::isLocal()) {
            return [
                'name' => 'Local',
                'bg_color' => '#505050',
                'fg_color' => '#ffffff',
            ];
        }

        // Unknown environment condition.
        return null;
    }
}
