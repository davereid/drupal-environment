<?php

namespace Davereid\DrupalEnvironment;

/**
 * The GitHub Workflow environment specifics.
 *
 * @see https://docs.github.com/en/actions/learn-github-actions/variables
 */
class GitHubWorkflowEnvironment extends DefaultEnvironment
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
