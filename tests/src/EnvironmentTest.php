<?php

namespace DrupalEnvironment\Tests;

use DrupalEnvironment\Environment;
use PHPUnit\Framework\TestCase;

/**
 * Tests the Environment.
 */
final class EnvironmentTest extends TestCase
{

    /**
     * Test the commandExists() method.
     */
    public function testCommandExists(): void
    {
        $this->assertTrue(Environment::commandExists('php'));
        $this->assertFalse(Environment::commandExists('invalid-command'));
    }

    /**
     * Test the environment methods.
     *
     * @dataProvider providerEnvironment
     */
    public function testEnvironment(array $variables, array $method_tests): void
    {
        $variables += [
            'ENVIRONMENT' => null,
            'PANTHEON_ENVIRONMENT' => null,
            // When running under CI, we need to ensure these are reset.
            'CI' => null,
            'GITLAB_CI' => null,
            'GITHUB_WORKFLOW' => null,
        ];
        $this->setEnvironmentVariables($variables);
        foreach ($method_tests as $name => $expected) {
            $this->assertSame($expected, Environment::$name(), "Asserting Environment::$name");
        }
    }

    /**
     * Set environment variables manually for testing.
     *
     * @param array $variables
     *   The variable values to set keyed by name.
     * @param array|null $originals
     *   If provided will be populated with the original variable values keyed by name.
     */
    protected function setEnvironmentVariables(array $variables, ?array &$originals = null): void
    {
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
    public function providerEnvironment(): array
    {
        return [
            'default-state' => [
                [],
                [
                    'getEnvironment' => '',
                    'isAcquia' => false,
                    'isCircleCi' => false,
                    'isGitHubWorkflow' => false,
                    'isGitLabCi' => false,
                    'isTugboat' => false,
                    'isPantheon' => false,
                    'isPlatformSh' => false,
                    'isProduction' => false,
                    'isStaging' => false,
                    'isDevelopment' => false,
                    'isPreview' => false,
                    'isCi' => false,
                    'isCli' => (PHP_SAPI === 'cli'),
                    'isLocal' => false,
                    'getIndicatorConfig' => null,
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
                    'isAcquia' => false,
                    'isCircleCi' => false,
                    'isGitHubWorkflow' => false,
                    'isGitLabCi' => false,
                    'isTugboat' => false,
                    'isPantheon' => false,
                    'isPlatformSh' => false,
                    'isProduction' => true,
                    'isStaging' => false,
                    'isDevelopment' => false,
                    'isPreview' => false,
                    'isCi' => false,
                    'isLocal' => false,
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
                    'isAcquia' => false,
                    'isCircleCi' => false,
                    'isGitHubWorkflow' => false,
                    'isGitLabCi' => false,
                    'isTugboat' => false,
                    'isPantheon' => false,
                    'isPlatformSh' => false,
                    'isProduction' => false,
                    'isStaging' => false,
                    'isDevelopment' => false,
                    'isPreview' => false,
                    'isCi' => false,
                    'isLocal' => false,
                    'getIndicatorConfig' => null,
                ],
            ],
            'pantheon-live' => [
                [
                    'PANTHEON_ENVIRONMENT' => 'live',
                ],
                [
                    'getEnvironment' => 'live',
                    'isAcquia' => false,
                    'isCircleCi' => false,
                    'isGitHubWorkflow' => false,
                    'isGitLabCi' => false,
                    'isTugboat' => false,
                    'isPantheon' => true,
                    'isPlatformSh' => false,
                    'isProduction' => true,
                    'isStaging' => false,
                    'isDevelopment' => false,
                    'isPreview' => false,
                    'isMultidev' => false,
                    'isCi' => false,
                    'isLocal' => false,
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
                    'isAcquia' => false,
                    'isCircleCi' => false,
                    'isGitHubWorkflow' => false,
                    'isGitLabCi' => false,
                    'isTugboat' => false,
                    'isPantheon' => true,
                    'isPlatformSh' => false,
                    'isProduction' => false,
                    'isStaging' => true,
                    'isDevelopment' => false,
                    'isPreview' => false,
                    'isMultidev' => false,
                    'isCi' => false,
                    'isLocal' => false,
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
                    'isAcquia' => false,
                    'isCircleCi' => false,
                    'isGitHubWorkflow' => false,
                    'isGitLabCi' => false,
                    'isTugboat' => false,
                    'isPantheon' => true,
                    'isPlatformSh' => false,
                    'isProduction' => false,
                    'isStaging' => false,
                    'isDevelopment' => true,
                    'isPreview' => false,
                    'isMultidev' => false,
                    'isCi' => false,
                    'isLocal' => false,
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
                    'isAcquia' => false,
                    'isCircleCi' => false,
                    'isGitHubWorkflow' => false,
                    'isGitLabCi' => false,
                    'isTugboat' => false,
                    'isPantheon' => true,
                    'isPlatformSh' => false,
                    'isProduction' => false,
                    'isStaging' => false,
                    'isDevelopment' => false,
                    'isPreview' => true,
                    'isMultidev' => true,
                    'isCi' => false,
                    'isLocal' => false,
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
                    'isAcquia' => false,
                    'isCircleCi' => false,
                    'isGitHubWorkflow' => false,
                    'isGitLabCi' => false,
                    'isTugboat' => false,
                    'isPantheon' => true,
                    'isPlatformSh' => false,
                    'isProduction' => false,
                    'isStaging' => false,
                    'isDevelopment' => false,
                    'isPreview' => false,
                    'isMultidev' => false,
                    'isCi' => true,
                    'isLocal' => false,
                    'getIndicatorConfig' => null,
                ],
            ],
            'pantheon-local' => [
                [
                    'PANTHEON_ENVIRONMENT' => 'local',
                ],
                [
                    'getEnvironment' => 'local',
                    'isAcquia' => false,
                    'isCircleCi' => false,
                    'isGitHubWorkflow' => false,
                    'isGitLabCi' => false,
                    'isTugboat' => false,
                    'isPantheon' => true,
                    'isPlatformSh' => false,
                    'isProduction' => false,
                    'isStaging' => false,
                    'isDevelopment' => false,
                    'isPreview' => false,
                    'isMultidev' => false,
                    'isCi' => false,
                    'isLocal' => true,
                    'getIndicatorConfig' => [
                        'name' => 'Local',
                        'bg_color' => '#ffffff',
                        'fg_color' => '#505050',
                    ],
                ],
            ],
            'platformsh-prod' => [
                [
                    'PLATFORM_ENVIRONMENT' => 'main-asdf123',
                    'PLATFORM_ENVIRONMENT_TYPE' => 'production',
                ],
                [
                    'getEnvironment' => 'main-asdf123',
                    'getEnvironmentType' => 'production',
                    'isAcquia' => false,
                    'isCircleCi' => false,
                    'isGitHubWorkflow' => false,
                    'isGitLabCi' => false,
                    'isTugboat' => false,
                    'isPantheon' => false,
                    'isPlatformSh' => true,
                    'isProduction' => true,
                    'isStaging' => false,
                    'isDevelopment' => false,
                    'isPreview' => false,
                    'isCi' => false,
                    'isLocal' => false,
                    'getIndicatorConfig' => [
                        'name' => 'Production',
                        'bg_color' => '#ffffff',
                        'fg_color' => '#e7131a',
                    ],
                ],
            ],
            'platformsh-stage' => [
                [
                    'PLATFORM_ENVIRONMENT' => 'stage-asdf123',
                    'PLATFORM_ENVIRONMENT_TYPE' => 'staging',
                ],
                [
                    'getEnvironment' => 'stage-asdf123',
                    'getEnvironmentType' => 'staging',
                    'isAcquia' => false,
                    'isCircleCi' => false,
                    'isGitHubWorkflow' => false,
                    'isGitLabCi' => false,
                    'isTugboat' => false,
                    'isPantheon' => false,
                    'isPlatformSh' => true,
                    'isProduction' => false,
                    'isStaging' => true,
                    'isDevelopment' => false,
                    'isPreview' => false,
                    'isCi' => false,
                    'isLocal' => false,
                    'getIndicatorConfig' => [
                        'name' => 'Staging',
                        'bg_color' => '#ffffff',
                        'fg_color' => '#b85c00',
                    ],
                ],
            ],
            'platformsh-dev' => [
                [
                    'PLATFORM_ENVIRONMENT' => 'develop-asdf123',
                    'PLATFORM_ENVIRONMENT_TYPE' => 'development',
                ],
                [
                    'getEnvironment' => 'develop-asdf123',
                    'getEnvironmentType' => 'development',
                    'isAcquia' => false,
                    'isCircleCi' => false,
                    'isGitHubWorkflow' => false,
                    'isGitLabCi' => false,
                    'isTugboat' => false,
                    'isPantheon' => false,
                    'isPlatformSh' => true,
                    'isProduction' => false,
                    'isStaging' => false,
                    'isDevelopment' => true,
                    'isPreview' => false,
                    'isCi' => false,
                    'isLocal' => false,
                    'getIndicatorConfig' => [
                        'name' => 'Development',
                        'bg_color' => '#ffffff',
                        'fg_color' => '#307b24',
                    ],
                ],
            ],
            'platformsh-preview' => [
                [
                    'PLATFORM_ENVIRONMENT' => 'pr-225-asdf123',
                    'PLATFORM_BRANCH' => 'pr-225',
                    'PLATFORM_ENVIRONMENT_TYPE' => 'development',
                ],
                [
                    'getEnvironment' => 'pr-225-asdf123',
                    'getEnvironmentType' => 'development',
                    'isAcquia' => false,
                    'isCircleCi' => false,
                    'isGitHubWorkflow' => false,
                    'isGitLabCi' => false,
                    'isTugboat' => false,
                    'isPantheon' => false,
                    'isPlatformSh' => true,
                    'isProduction' => false,
                    'isStaging' => false,
                    'isDevelopment' => false,
                    'isPreview' => true,
                    'isCi' => false,
                    'isLocal' => false,
                    'getIndicatorConfig' => [
                      'name' => 'Preview',
                      'bg_color' => '#ffffff',
                      'fg_color' => '#990055',
                    ],
                ],
            ],
            'tugboat' => [
                [
                    'TUGBOAT_PREVIEW_NAME' => 'phpunit',
                ],
                [
                    'getEnvironment' => 'phpunit',
                    'isAcquia' => false,
                    'isCircleCi' => false,
                    'isGitHubWorkflow' => false,
                    'isGitLabCi' => false,
                    'isTugboat' => true,
                    'isPantheon' => false,
                    'isPlatformSh' => false,
                    'isProduction' => false,
                    'isStaging' => false,
                    'isDevelopment' => false,
                    'isPreview' => true,
                    'isCi' => false,
                    'isLocal' => false,
                    'getIndicatorConfig' => [
                        'name' => 'Preview',
                        'bg_color' => '#ffffff',
                        'fg_color' => '#990055',
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
                    'isAcquia' => false,
                    'isCircleCi' => true,
                    'isGitHubWorkflow' => false,
                    'isGitLabCi' => false,
                    'isTugboat' => false,
                    'isPantheon' => false,
                    'isPlatformSh' => false,
                    'isProduction' => false,
                    'isStaging' => false,
                    'isDevelopment' => false,
                    'isPreview' => false,
                    'isCi' => true,
                    'isLocal' => false,
                    'getIndicatorConfig' => null,
                ],
            ],
            'github' => [
                [
                    'CI' => 'true',
                    'GITHUB_WORKFLOW' => 'test'
                ],
                [
                    'getEnvironment' => 'ci',
                    'isAcquia' => false,
                    'isCircleCi' => false,
                    'isGitHubWorkflow' => true,
                    'isGitLabCi' => false,
                    'isTugboat' => false,
                    'isPantheon' => false,
                    'isPlatformSh' => false,
                    'isProduction' => false,
                    'isStaging' => false,
                    'isDevelopment' => false,
                    'isPreview' => false,
                    'isCi' => true,
                    'isLocal' => false,
                    'getIndicatorConfig' => null,
                ],
            ],
            'gitlab' => [
                [
                    'CI' => 'true',
                    'GITLAB_CI' => 'true'
                ],
                [
                    'getEnvironment' => 'ci',
                    'isAcquia' => false,
                    'isCircleCi' => false,
                    'isGitHubWorkflow' => false,
                    'isGitLabCi' => true,
                    'isTugboat' => false,
                    'isPantheon' => false,
                    'isPlatformSh' => false,
                    'isProduction' => false,
                    'isStaging' => false,
                    'isDevelopment' => false,
                    'isPreview' => false,
                    'isCi' => true,
                    'isLocal' => false,
                    'getIndicatorConfig' => null,
                ],
            ],
            'ddev' => [
                [
                    'IS_DDEV_PROJECT' => true,
                ],
                [
                    'getEnvironment' => '',
                    'isAcquia' => false,
                    'isCircleCi' => false,
                    'isGitHubWorkflow' => false,
                    'isGitLabCi' => false,
                    'isTugboat' => false,
                    'isPantheon' => false,
                    'isPlatformSh' => false,
                    'isProduction' => false,
                    'isStaging' => false,
                    'isDevelopment' => false,
                    'isPreview' => false,
                    'isCi' => false,
                    'isLocal' => true,
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
                    'isAcquia' => false,
                    'isCircleCi' => false,
                    'isGitHubWorkflow' => false,
                    'isGitLabCi' => false,
                    'isTugboat' => false,
                    'isPantheon' => false,
                    'isPlatformSh' => false,
                    'isProduction' => false,
                    'isStaging' => false,
                    'isDevelopment' => false,
                    'isPreview' => false,
                    'isCi' => false,
                    'isLocal' => true,
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
