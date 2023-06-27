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
     * Test the commandExists() method.
     *
     * @covers \Davereid\DrupalEnvironment\Environment::commandExists
     */
    public function testCommandExists(): void {
        $this->assertTrue(Environment::commandExists('php'));
        $this->assertFalse(Environment::commandExists('invalid-command'));
    }

    /**
     * Test the environment methods.
     *
     * @dataProvider providerEnvironment
     */
    public function testEnvironment(array $variables, array $method_tests): void {
        $variables += [
            'ENVIRONMENT' => NULL,
            'PANTHEON_ENVIRONMENT' => NULL,
            // When running under CI, we need to ensure these are reset.
            'CI' => NULL,
            'GITLAB_CI' => NULL,
            'GITHUB_WORKFLOW' => NULL,
        ];
        $originals = [];
        $this->setEnvironmentVariables($variables, $originals);
        foreach ($method_tests as $name => $expected) {
            $this->assertSame($expected, Environment::$name(), "Asserting Environment::$name");
        }
        $this->setEnvironmentVariables($originals);
    }

    /**
     * @param array $variables
     * @param array|null $originals
     */
    protected function setEnvironmentVariables(array $variables, ?array &$originals = NULL): void {
        foreach ($variables as $name => $value) {
            if (isset($originals)) {
                $originals[$name] = getenv($name) ?: null;
            }
            isset($value) ? putenv("$name=$value") : putenv($name);
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
                    'isCli' => (PHP_SAPI === 'cli'),
                    'isLocal' => FALSE,
                    'getIndicatorConfig' => NULL,
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
                    'getIndicatorConfig' => [
                        'name' => 'Production',
                        'bg_color' => '#ffffff',
                        'fg_color' => '#e7131a',
                    ],
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
                    'getIndicatorConfig' => NULL,
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
                    'getIndicatorConfig' => [
                        'name' => 'Production',
                        'bg_color' => '#ffffff',
                        'fg_color' => '#e7131a',
                    ],
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
                    'getIndicatorConfig' => [
                        'name' => 'Staging',
                        'bg_color' => '#ffffff',
                        'fg_color' => '#b85c00',
                    ],
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
                    'getIndicatorConfig' => [
                        'name' => 'Development',
                        'bg_color' => '#ffffff',
                        'fg_color' => '#307b24',
                    ],
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
                    'getIndicatorConfig' => [
                        'name' => 'Preview',
                        'bg_color' => '#ffffff',
                        'fg_color' => '#990055',
                    ],
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
                    'getIndicatorConfig' => NULL,
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
                    'getIndicatorConfig' => [
                        'name' => 'Local',
                        'bg_color' => '#ffffff',
                        'fg_color' => '#505050',
                    ],
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
                    'getIndicatorConfig' => NULL,
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
                    'getIndicatorConfig' => NULL,
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
                    'getIndicatorConfig' => NULL,
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
                    'getIndicatorConfig' => [
                        'name' => 'Local',
                        'bg_color' => '#ffffff',
                        'fg_color' => '#505050',
                    ],
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
                    'getIndicatorConfig' => [
                        'name' => 'Local',
                        'bg_color' => '#ffffff',
                        'fg_color' => '#505050',
                    ],
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
