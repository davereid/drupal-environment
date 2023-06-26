<?php

namespace Davereid\DrupalEnvironment;

/**
 * The CircleCI environment specifics.
 *
 * @see https://circleci.com/docs/variables/
 */
class CircleCiEnvironment extends DefaultEnvironment
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

    /**
     * {@inheritdoc}
     */
    public static function isCi(): bool
    {
        return TRUE;
    }


}
