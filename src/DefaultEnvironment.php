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
     * Determine if this is a preview environment.
     *
     * @return bool
     *   TRUE if this is a preview environment.
     */
    public static function isPreview(): bool
    {
        return Environment::isTugboat();
    }

}
