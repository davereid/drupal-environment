<?php

namespace Davereid\DrupalEnvironment;

/**
 * The Acquia environment specifics.
 *
 * @see https://docs.acquia.com/cloud-platform/develop/env-variable.html
 *
 * @todo Add isPreview() support.
 */
class AcquiaEnvironment extends DefaultEnvironment
{

    /**
     * {@inheritdoc}
     */
    public const ENVIRONMENT_NAME = 'AH_SITE_ENVIRONMENT';

}
