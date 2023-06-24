<?php

namespace Davereid\DrupalEnvironment;

/**
 * Helpers for working with the environment.
 *
 * @todo Add isPreview() support.
 *
 * This class should be usable outside of Drupal.
 */
class AcquiaEnvironment extends DefaultEnvironment
{

    /**
     * {@inheritdoc}
     */
    public const ENVIRONMENT_NAME = 'AH_SITE_ENVIRONMENT';

}
