<?php
declare(strict_types=1);

namespace DrupalEnvironment;

/**
 * The GitHub Workflow environment specifics.
 *
 * @see https://docs.github.com/en/actions/learn-github-actions/variables
 *
 * @internal
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
    public static function getEnvironment(): string
    {
        return static::CI;
    }
}
