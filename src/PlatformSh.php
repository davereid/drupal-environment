<?php

namespace DrupalEnvironment;

/**
 * The Platform.sh environment specifics.
 *
 * @see https://docs.platform.sh/environments.html
 *
 * @internal
 */
class PlatformSh extends DefaultEnvironment
{

    /**
     * The default environment variable name.
     *
     * @var string
     */
    public const ENVIRONMENT_TYPE = 'PLATFORM_ENVIRONMENT_TYPE';

    /**
     * {@inheritdoc}
     */
    public const ENVIRONMENT_NAME = 'PLATFORM_ENVIRONMENT';

    /**
     * {@inheritdoc}
     */
    public const PRODUCTION = 'production';

    /**
     * {@inheritdoc}
     */
    public const STAGING = 'staging';

    /**
     * {@inheritdoc}
     */
    public const DEVELOPMENT = 'development';

    /**
     * Return the environment type.
     *
     * For example: "local" or "development" or "production".
     *
     * @return string
     *   The type of the environment.
     */
    public static function getEnvironmentType(): string
    {
        return static::get(static::ENVIRONMENT_TYPE) ?: 'local';
    }

    /**
     * {@inheritdoc}
     */
    public static function isProduction(): bool
    {
        return static::getEnvironmentType() === static::PRODUCTION;
    }

    /**
     * {@inheritdoc}
     */
    public static function isStaging(): bool
    {
        return static::getEnvironmentType() === static::STAGING;
    }

    /**
     * {@inheritdoc}
     */
    public static function isDevelopment(): bool
    {
        return static::getEnvironmentType() === static::DEVELOPMENT && !static::isPreview();
    }

    /**
     * {@inheritdoc}
     */
    public static function isPreview(): bool
    {
        // If the branch looks like "pr-123" then it's a pull request environment.
        return preg_match('/^pr-\d+$/', static::get("PLATFORM_BRANCH"));
    }
}
