<?php

declare(strict_types=1);

namespace DrupalEnvironment;

/**
 * The Upsun (formerly Platform.sh) environment specifics.
 *
 * Upsun Cloud and Upsun Fixed provide the same variables.
 *
 * @see https://developer.upsun.com/docs/development/variables/use-variables
 * @see https://fixed.docs.upsun.com/development/variables/use-variables.html
 *
 * @internal
 */
class Upsun extends DefaultEnvironment
{
    /**
     * The environment type variable name.
     *
     * @var string
     */
    public const ENVIRONMENT_TYPE = 'PLATFORM_ENVIRONMENT_TYPE';

    /**
     * {@inheritdoc}
     *
     * PLATFORM_ENVIRONMENT is the generated machine name, like "main-abcd123",
     * so use the branch, which is the environment name the user chose.
     */
    public const ENVIRONMENT_NAME = 'PLATFORM_BRANCH';

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
        // Lando and DDEV emulate an Upsun development environment locally.
        return static::getEnvironment() === static::DEVELOPMENT && !static::isPreview() && !static::isLocal();
    }

    /**
     * {@inheritdoc}
     */
    public static function isPreview(): bool
    {
        // If the branch looks like "pr-123" then it's a pull request environment.
        return !static::isLocal() && (bool) preg_match('/^pr-\d+$/', (string) static::getEnvironmentName());
    }
}
