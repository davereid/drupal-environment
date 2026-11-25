<?php

declare(strict_types=1);

namespace DrupalEnvironment;

/**
 * The GitlabCi environment specifics.
 *
 * @see https://docs.gitlab.com/ee/ci/variables/predefined_variables.html
 *
 * @internal
 *
 * @deprecated in davereid/drupal-environment:1.4.0 and is removed from
 *   davereid/drupal-environment:2.0.0. Use Environment::isCi() instead.
 */
class GitLabCi extends DefaultEnvironment
{
    /**
     * {@inheritdoc}
     */
    public const ENVIRONMENT_NAME = 'GITLAB_CI';

    /**
     * {@inheritdoc}
     */
    public static function getEnvironment(): string|bool
    {
        return parent::getEnvironment() ? static::CI : false;
    }
}
