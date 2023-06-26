<?php

namespace Davereid\DrupalEnvironment;

/**
 * The GitlabCi environment specifics.
 *
 * @see https://docs.gitlab.com/ee/ci/variables/predefined_variables.html
 */
class GitLabCiEnvironment extends DefaultEnvironment
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
