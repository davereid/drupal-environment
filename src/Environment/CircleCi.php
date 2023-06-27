<?php

namespace Davereid\DrupalEnvironment\Environment;

/**
 * The CircleCI environment specifics.
 *
 * @see https://circleci.com/docs/variables/
 */
class CircleCi extends DefaultEnvironment
{

    /**
     * {@inheritdoc}
     */
    public const ENVIRONMENT_NAME = 'CIRCLECI';

    /**
     * {@inheritdoc}
     */
    public static function getEnvironment(): string
    {
        return static::CI;
    }
}
