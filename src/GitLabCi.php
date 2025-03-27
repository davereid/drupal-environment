<?php
declare(strict_types=1);

namespace DrupalEnvironment;

/**
 * The GitlabCi environment specifics.
 *
 * @see https://docs.gitlab.com/ee/ci/variables/predefined_variables.html
 *
 * @internal
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
    public static function getEnvironment(): string
    {
        return static::CI;
    }
}
