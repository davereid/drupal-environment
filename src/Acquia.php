<?php

declare(strict_types=1);

namespace DrupalEnvironment;

/**
 * The Acquia environment specifics.
 *
 * @see https://docs.acquia.com/cloud-platform/manage/environments/
 * @see https://docs.acquia.com/cloud-platform/develop/env-variable.html
 *
 * @todo Add isPreview() support.
 * @todo Should the 'ide' environment be detected as local?
 * @todo Add support for https://docs.acquia.com/ra/environment.html
 *
 * @internal
 */
class Acquia extends DefaultEnvironment
{
    /**
     * {@inheritdoc}
     */
    public const ENVIRONMENT_NAME = 'AH_SITE_ENVIRONMENT';

    /**
     * The Site Factory production environment name.
     *
     * @see https://docs.acquia.com/acquia-cloud-platform/using-environment-variables
     */
    public const SITE_FACTORY_PRODUCTION = '01live';

    /**
     * The Site Factory staging environment name.
     */
    public const SITE_FACTORY_STAGING = '01test';

    /**
     * The Site Factory development environment name.
     */
    public const SITE_FACTORY_DEVELOPMENT = '01dev';

    /**
     * {@inheritdoc}
     */
    public static function isProduction(): bool
    {
        return parent::isProduction() || static::getEnvironment() === static::SITE_FACTORY_PRODUCTION;
    }

    /**
     * {@inheritdoc}
     */
    public static function isStaging(): bool
    {
        return parent::isStaging() || static::getEnvironment() === static::SITE_FACTORY_STAGING;
    }

    /**
     * {@inheritdoc}
     */
    public static function isDevelopment(): bool
    {
        return parent::isDevelopment() || static::getEnvironment() === static::SITE_FACTORY_DEVELOPMENT;
    }
}
