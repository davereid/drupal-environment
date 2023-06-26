<?php

namespace Davereid\DrupalEnvironment\Tests;

use Davereid\DrupalEnvironment\Environment;
use PHPUnit\Framework\TestCase;

/**
 * Tests the Environment.
 */
final class EnvironmentTest extends TestCase
{

    /**
     * Test the environment methods.
     *
     * @dataProvider providerEnvironment
     */
    public function testEnvironment(array $environment_variables, array $method_tests): void {
        $environment_variables += [
            'ENVIRONMENT' => NULL,
            'CI' => NULL,
            'GITLAB_CI' => NULL,
        ];
        foreach ($environment_variables as $name => $value) {
            isset($value) ? putenv("$name=$value") : putenv($name);
        }
        foreach ($method_tests as $name => $expected) {
            $this->assertSame($expected, Environment::$name(), "Asserting Environment::$name");
        }
    }

    /**
     * Data provider for ::testEnvironment.
     */
    public function providerEnvironment(): array {
        return [
            'default-state' => [
                [],
                [
                    'getEnvironment' => '',
                    'isAcquia' => FALSE,
                    'isCircleCi' => FALSE,
                    'isGitHubWorkflow' => FALSE,
                    'isGitLabCi' => FALSE,
                    'isPantheon' => FALSE,
                    'isProduction' => FALSE,
                    'isStaging' => FALSE,
                    'isDevelopment' => FALSE,
                    'isPreview' => FALSE,
                    'isCi' => FALSE,
                    'isLocal' => FALSE,
                    'getComposerFilename' => 'composer.json',
                    'getComposerLockFilename' => 'composer.lock',
                ],
            ],
            'default-prod' => [
                [
                    'ENVIRONMENT' => 'prod'
                ],
                [
                    'getEnvironment' => 'prod',
                    'isAcquia' => FALSE,
                    'isCircleCi' => FALSE,
                    'isGitHubWorkflow' => FALSE,
                    'isGitLabCi' => FALSE,
                    'isPantheon' => FALSE,
                    'isProduction' => TRUE,
                    'isStaging' => FALSE,
                    'isDevelopment' => FALSE,
                    'isPreview' => FALSE,
                    'isCi' => FALSE,
                    'isLocal' => FALSE,
                ],
            ],
            'pantheon-empty' => [
                [
                    'PANTHEON_ENVIRONMENT' => '',
                ],
                [
                    'getEnvironment' => '',
                    'isAcquia' => FALSE,
                    'isCircleCi' => FALSE,
                    'isGitHubWorkflow' => FALSE,
                    'isGitLabCi' => FALSE,
                    'isPantheon' => FALSE,
                    'isProduction' => FALSE,
                    'isStaging' => FALSE,
                    'isDevelopment' => FALSE,
                    'isPreview' => FALSE,
                    'isCi' => FALSE,
                    'isLocal' => FALSE,
                ],
            ],
            'pantheon-live' => [
                [
                    'PANTHEON_ENVIRONMENT' => 'live',
                ],
                [
                    'getEnvironment' => 'live',
                    'isAcquia' => FALSE,
                    'isCircleCi' => FALSE,
                    'isGitHubWorkflow' => FALSE,
                    'isGitLabCi' => FALSE,
                    'isPantheon' => TRUE,
                    'isProduction' => TRUE,
                    'isStaging' => FALSE,
                    'isDevelopment' => FALSE,
                    'isPreview' => FALSE,
                    'isMultidev' => FALSE,
                    'isCi' => FALSE,
                    'isLocal' => FALSE,
                ],
            ],
            'pantheon-test' => [
                [
                    'PANTHEON_ENVIRONMENT' => 'test',
                ],
                [
                    'getEnvironment' => 'test',
                    'isAcquia' => FALSE,
                    'isCircleCi' => FALSE,
                    'isGitHubWorkflow' => FALSE,
                    'isGitLabCi' => FALSE,
                    'isPantheon' => TRUE,
                    'isProduction' => FALSE,
                    'isStaging' => TRUE,
                    'isDevelopment' => FALSE,
                    'isPreview' => FALSE,
                    'isMultidev' => FALSE,
                    'isCi' => FALSE,
                    'isLocal' => FALSE,
                ],
            ],
            'pantheon-dev' => [
                [
                    'PANTHEON_ENVIRONMENT' => 'dev',
                ],
                [
                    'getEnvironment' => 'dev',
                    'isAcquia' => FALSE,
                    'isCircleCi' => FALSE,
                    'isGitHubWorkflow' => FALSE,
                    'isGitLabCi' => FALSE,
                    'isPantheon' => TRUE,
                    'isProduction' => FALSE,
                    'isStaging' => FALSE,
                    'isDevelopment' => TRUE,
                    'isPreview' => FALSE,
                    'isMultidev' => FALSE,
                    'isCi' => FALSE,
                    'isLocal' => FALSE,
                ],
            ],
            'pantheon-multidev' => [
                [
                    'PANTHEON_ENVIRONMENT' => 'pr-1',
                ],
                [
                    'getEnvironment' => 'pr-1',
                    'isAcquia' => FALSE,
                    'isCircleCi' => FALSE,
                    'isGitHubWorkflow' => FALSE,
                    'isGitLabCi' => FALSE,
                    'isPantheon' => TRUE,
                    'isProduction' => FALSE,
                    'isStaging' => FALSE,
                    'isDevelopment' => FALSE,
                    'isPreview' => TRUE,
                    'isMultidev' => TRUE,
                    'isCi' => FALSE,
                    'isLocal' => FALSE,
                ],
            ],
            'pantheon-ci' => [
                [
                    'PANTHEON_ENVIRONMENT' => 'ci',
                ],
                [
                    'getEnvironment' => 'ci',
                    'isAcquia' => FALSE,
                    'isCircleCi' => FALSE,
                    'isGitHubWorkflow' => FALSE,
                    'isGitLabCi' => FALSE,
                    'isPantheon' => TRUE,
                    'isProduction' => FALSE,
                    'isStaging' => FALSE,
                    'isDevelopment' => FALSE,
                    'isPreview' => FALSE,
                    'isMultidev' => FALSE,
                    'isCi' => TRUE,
                    'isLocal' => FALSE,
                ],
            ],
            'pantheon-local' => [
                [
                    'PANTHEON_ENVIRONMENT' => 'local',
                ],
                [
                    'getEnvironment' => 'local',
                    'isAcquia' => FALSE,
                    'isCircleCi' => FALSE,
                    'isGitHubWorkflow' => FALSE,
                    'isGitLabCi' => FALSE,
                    'isPantheon' => TRUE,
                    'isProduction' => FALSE,
                    'isStaging' => FALSE,
                    'isDevelopment' => FALSE,
                    'isPreview' => FALSE,
                    'isMultidev' => FALSE,
                    'isCi' => FALSE,
                    'isLocal' => TRUE,
                ],
            ],
            'circleci' => [
                [
                    'CI' => 'true',
                    'CIRCLECI' => 'true'
                ],
                [
                    'getEnvironment' => 'ci',
                    'isAcquia' => FALSE,
                    'isCircleCi' => TRUE,
                    'isGitHubWorkflow' => FALSE,
                    'isGitLabCi' => FALSE,
                    'isPantheon' => FALSE,
                    'isProduction' => FALSE,
                    'isStaging' => FALSE,
                    'isDevelopment' => FALSE,
                    'isPreview' => FALSE,
                    'isCi' => TRUE,
                    'isLocal' => FALSE,
                ],
            ],
            'github' => [
                [
                    'CI' => 'true',
                    'GITHUB_WORKFLOW' => 'test'
                ],
                [
                    'getEnvironment' => 'ci',
                    'isAcquia' => FALSE,
                    'isCircleCi' => FALSE,
                    'isGitHubWorkflow' => TRUE,
                    'isGitLabCi' => FALSE,
                    'isPantheon' => FALSE,
                    'isProduction' => FALSE,
                    'isStaging' => FALSE,
                    'isDevelopment' => FALSE,
                    'isPreview' => FALSE,
                    'isCi' => TRUE,
                    'isLocal' => FALSE,
                ],
            ],
            'gitlab' => [
                [
                    'CI' => 'true',
                    'GITLAB_CI' => 'true'
                ],
                [
                    'getEnvironment' => 'ci',
                    'isAcquia' => FALSE,
                    'isCircleCi' => FALSE,
                    'isGitHubWorkflow' => FALSE,
                    'isGitLabCi' => TRUE,
                    'isPantheon' => FALSE,
                    'isProduction' => FALSE,
                    'isStaging' => FALSE,
                    'isDevelopment' => FALSE,
                    'isPreview' => FALSE,
                    'isCi' => TRUE,
                    'isLocal' => FALSE,
                ],
            ],
            'ddev' => [
                [
                    'IS_DDEV_PROJECT' => TRUE,
                ],
                [
                    'getEnvironment' => '',
                    'isAcquia' => FALSE,
                    'isCircleCi' => FALSE,
                    'isGitHubWorkflow' => FALSE,
                    'isGitLabCi' => FALSE,
                    'isPantheon' => FALSE,
                    'isProduction' => FALSE,
                    'isStaging' => FALSE,
                    'isDevelopment' => FALSE,
                    'isPreview' => FALSE,
                    'isCi' => FALSE,
                    'isLocal' => TRUE,
                ],
            ],
            'lando' => [
                [
                    'LANDO_INFO' => '[...]',
                ],
                [
                    'getEnvironment' => '',
                    'isAcquia' => FALSE,
                    'isCircleCi' => FALSE,
                    'isGitHubWorkflow' => FALSE,
                    'isGitLabCi' => FALSE,
                    'isPantheon' => FALSE,
                    'isProduction' => FALSE,
                    'isStaging' => FALSE,
                    'isDevelopment' => FALSE,
                    'isPreview' => FALSE,
                    'isCi' => FALSE,
                    'isLocal' => TRUE,
                ],
            ],
            'composer' => [
                [
                    'COMPOSER' => 'alternate.ext',
                ],
                [
                    'getComposerFilename' => 'alternate.ext',
                    'getComposerLockFilename' => 'alternate.lock',
                ]
            ]
        ];
    }

}
