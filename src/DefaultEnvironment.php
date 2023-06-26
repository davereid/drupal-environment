<?php

namespace Davereid\DrupalEnvironment;

/**
 * The standard environment.
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
    public const PRODUCTION = 'production';

    /**
     * The default staging environment name.
     *
     * @var string
     */
    public const STAGING = 'staging';

    /**
     * The default development environment name.
     *
     * @var string
     */
    public const DEVELOPMENT = 'dev';

    /**
     * The default CI environment name.
     *
     * @var string
     */
    public const CI = 'ci';

    /**
     * Return the environment name.
     *
     * For example: "local" or "ci" or "dev" or "prod".
     *
     * @return string
     *   The name of the environment.
     */
    public static function getEnvironment(): string
    {
        return Environment::get(static::ENVIRONMENT_NAME);
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
        return Environment::isTugboat();
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

}
