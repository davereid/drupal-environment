<?php

declare(strict_types=1);

namespace DrupalEnvironment;

/**
 * The CircleCI environment specifics.
 *
 * @see https://circleci.com/docs/variables/
 *
 * @internal
 *
 * @deprecated in davereid/drupal-environment:1.4.0 and is removed from
 *   davereid/drupal-environment:2.0.0. Use Environment::isCi() instead.
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
    public static function getEnvironment(): string|bool
    {
        return parent::getEnvironment() ? static::CI : false;
    }
}
