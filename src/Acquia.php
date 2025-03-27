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
}
