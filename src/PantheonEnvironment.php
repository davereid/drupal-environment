<?php

namespace Davereid\DrupalEnvironment;

/**
 * The Pantheon environment specifics.
 */
class PantheonEnvironment extends DefaultEnvironment
{

    /**
     * {@inheritdoc}
     */
    public const ENVIRONMENT_NAME = 'PANTHEON_ENVIRONMENT';

    /**
     * {@inheritdoc}
     */
    public const PRODUCTION = 'live';

    /**
     * {@inheritdoc}
     */
    public const STAGING = 'test';

    /**
     * {@inheritdoc}
     */
    public static function isPreview(): bool
    {
        return static::isMultidev() || parent::isPreview();
    }

    /**
     * Determine if this is a Pantheon Multidev environment.
     *
     * @return bool
     *   TRUE if this is a Pantheon Multidev environment.
     */
    public static function isMultidev(): bool
    {
        return !static::isProduction()
            && !static::isStaging()
            && !static::isDevelopment()
            && !static::isCi()
            && Environment::isLocal();
    }

}
