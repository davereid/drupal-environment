<?php
declare(strict_types=1);

namespace DrupalEnvironment;

/**
 * The Pantheon environment specifics.
 *
 * @see https://docs.pantheon.io/pantheon-workflow
 * @see https://docs.pantheon.io/guides/multidev
 *
 * @internal
 */
class Pantheon extends DefaultEnvironment
{

    /**
     * {@inheritdoc}
     */
    public const ENVIRONMENT_NAME = 'PANTHEON_ENVIRONMENT';

    /**
     * {@inheritdoc}
     */
    public const PRODUCTION = 'live';

    public const INTERNAL_DOMAIN = 'pantheonsite.io';

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
        // Because the PANTHEON_ENVIRONMENT could be potentially anything,
        // we need to narrow out all the known environments.
        return !static::isProduction()
            && !static::isStaging()
            && !static::isDevelopment()
            && !static::isCi()
            && !static::isLocal();
    }

    /**
     * Determine if this is a public domain request.
     *
     * @return bool
     */
    public static function isCustomDomain(): bool
    {
        return isset($_SERVER['HTTP_HOST']) && !str_contains($_SERVER['HTTP_HOST'], static::INTERNAL_DOMAIN);
    }
}
