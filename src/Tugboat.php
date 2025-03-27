<?php
declare(strict_types=1);

namespace DrupalEnvironment;

/**
 * The Tugboat environment specifics.
 *
 * @see https://docs.tugboatqa.com/reference/environment-variables/
 *
 * @internal
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
        return true;
    }
}
