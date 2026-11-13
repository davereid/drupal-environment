<?php

declare(strict_types=1);

namespace DrupalEnvironment;

/**
 * The Upsun (formerly Platform.sh) environment specifics.
 *
 * Upsun Cloud and Upsun Fixed provide the same variables. They are only set at
 * runtime, so Upsun is not detected during the build hook.
 *
 * @see https://developer.upsun.com/docs/development/variables/use-variables
 * @see https://fixed.docs.upsun.com/development/variables/use-variables.html
 *
 * @internal
 */
class Upsun extends DefaultEnvironment
{
    /**
     * {@inheritdoc}
     *
     * Upsun sets the environment type, not the environment name, in this
     * variable.
     */
    public const ENVIRONMENT_NAME = 'PLATFORM_ENVIRONMENT_TYPE';

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
     *
     * Lando and DDEV emulate an Upsun development environment, so return
     * "local" there. Pull request environments, like "pr-123", return
     * "preview". Other development environments return "development".
     */
    public static function getEnvironment(): string|bool
    {
        $type = parent::getEnvironment();
        return match (true) {
            !$type => false,
            Environment::isDdev() || Environment::isLando() => 'local',
            $type === static::DEVELOPMENT
                && (bool) preg_match('/^pr-\d+$/', (string) static::getEnvironmentName()) => static::PREVIEW,
            default => $type,
        };
    }

    /**
     * {@inheritdoc}
     *
     * PLATFORM_ENVIRONMENT is the generated machine name, like "main-abcd123",
     * so use the branch, which is the environment name the user chose.
     */
    public static function getEnvironmentName(): string|bool
    {
        return Environment::get('PLATFORM_BRANCH') ?: false;
    }
}
