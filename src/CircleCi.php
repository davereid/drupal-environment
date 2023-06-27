<?php

namespace Davereid\DrupalEnvironment;

/**
 * The CircleCI environment specifics.
 *
 * @see https://circleci.com/docs/variables/
 *
 * @internal
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
