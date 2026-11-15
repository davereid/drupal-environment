<?php

declare(strict_types=1);

namespace DrupalEnvironment;

/**
 * Any CI service that sets the common CI variable.
 *
 * Most CI services set CI=true, including Bitbucket Pipelines, Buildkite, and
 * Travis CI. This is checked after the named CI classes, so they still win.
 *
 * @internal
 */
class GenericCi extends DefaultEnvironment
{
    /**
     * {@inheritdoc}
     */
    public const ENVIRONMENT_NAME = 'CI';

    /**
     * {@inheritdoc}
     */
    public static function getEnvironment(): string|bool
    {
        // Some build tools document CI=false as a way to turn off CI behavior.
        $value = parent::getEnvironment();
        return $value && $value !== 'false' ? static::CI : false;
    }
}
