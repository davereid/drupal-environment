<?php

declare(strict_types=1);

namespace DrupalEnvironment;

/**
 * The GitHub Workflow environment specifics.
 *
 * @see https://docs.github.com/en/actions/learn-github-actions/variables
 *
 * @internal
 *
 * @deprecated in davereid/drupal-environment:1.4.0 and is removed from
 *   davereid/drupal-environment:2.0.0. Use Environment::isCi() instead.
 */
class GitHubWorkflow extends DefaultEnvironment
{
    /**
     * {@inheritdoc}
     */
    public const ENVIRONMENT_NAME = 'GITHUB_WORKFLOW';

    /**
     * {@inheritdoc}
     */
    public static function getEnvironment(): string|bool
    {
        return parent::getEnvironment() ? static::CI : false;
    }
}
