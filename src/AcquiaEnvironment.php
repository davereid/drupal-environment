<?php

namespace Davereid\DrupalEnvironment;

/**
 * The Acquia environment specifics.
 *
 * @see https://docs.acquia.com/cloud-platform/manage/environments/
 * @see https://docs.acquia.com/cloud-platform/develop/env-variable.html
 *
 * @todo Add isPreview() support.
 * @todo Should the 'ide' environment be detected as local?
 * @todo Add support for https://docs.acquia.com/ra/environment.html
 */
class AcquiaEnvironment extends DefaultEnvironment
{

    /**
     * {@inheritdoc}
     */
    public const ENVIRONMENT_NAME = 'AH_SITE_ENVIRONMENT';

}
