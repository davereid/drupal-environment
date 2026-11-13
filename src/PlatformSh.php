<?php

declare(strict_types=1);

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
     * {@inheritdoc}
     */
    public static function getEnvironment(): string|bool
    {
        return Environment::get(static::ENVIRONMENT_TYPE) ?: false;
    }

    /**
     * Return the user-defined environment name.
     *
     * @return string|bool
     *   The name of the environment.
     */
    public static function getEnvironmentName(): string|bool
    {
        return Environment::get(static::ENVIRONMENT_NAME) ?: false;
    }

    /**
     * {@inheritdoc}
     */
    public static function isDevelopment(): bool
    {
        return static::getEnvironment() === static::DEVELOPMENT && !static::isPreview();
    }

    /**
     * {@inheritdoc}
     */
    public static function isPreview(): bool
    {
        // If the branch looks like "pr-123" then it's a pull request environment.
        return (bool) preg_match('/^pr-\d+$/', (string) Environment::get('PLATFORM_BRANCH'));
    }
}
