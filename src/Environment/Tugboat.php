<?php

namespace Davereid\DrupalEnvironment\Environment;

/**
 * The Tugboat environment specifics.
 *
 * @see https://docs.tugboatqa.com/reference/environment-variables/
 */
class Tugboat extends DefaultEnvironment
{

    /**
     * {@inheritdoc}
     */
    public const ENVIRONMENT_NAME = 'TUGBOAT_PREVIEW_NAME';

    /**
     * {@inheritdoc}
     */
    public static function isPreview(): bool
    {
        return TRUE;
    }

}
