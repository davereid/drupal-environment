<?php

declare(strict_types=1);

namespace DrupalEnvironment;

/**
 * The Lagoon environment specifics.
 *
 * Lagoon sets the environment type, not the environment name, in a variable
 * available to every container.
 *
 * @see https://docs.lagoon.sh/concepts-advanced/environment-types/
 *
 * @internal
 */
class Lagoon extends DefaultEnvironment
{
    /**
     * {@inheritdoc}
     */
    public const ENVIRONMENT_NAME = 'LAGOON_ENVIRONMENT_TYPE';

    /**
     * {@inheritdoc}
     */
    public const PRODUCTION = 'production';

    /**
     * {@inheritdoc}
     */
    public const DEVELOPMENT = 'development';
}
