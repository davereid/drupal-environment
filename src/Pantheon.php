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

    /**
     * The internal pantheon domain name.
     */
    public const PLATFORM_DOMAIN = '.pantheonsite.io';

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
     *
     * @see https://docs.pantheon.io/guides/multidev
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
     * Determine if this is a custom domain request.
     *
     * @return bool
     *
     * @see https://docs.pantheon.io/guides/domains
     */
    public static function isCustomDomain(): bool
    {
        return !str_ends_with(Environment::getHost(), static::PLATFORM_DOMAIN);
    }
}
