<?php
declare(strict_types=1);

namespace DrupalEnvironment\Tests;

use DrupalEnvironment\Environment;
use PHPUnit\Event\RuntimeException;
use PHPUnit\Framework\Attributes\DataProvider;
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
    #[DataProvider('providerEnvironment')]
    public function testEnvironment(array $variables, array $method_tests): void
    {
        $variables += [
            'ENV' => [],
        ];
        $variables['ENV'] += [
            'ENVIRONMENT' => null,
            'PANTHEON_ENVIRONMENT' => null,
            // When running under CI, we need to ensure these are reset.
            'CI' => null,
            'GITLAB_CI' => null,
            'GITHUB_WORKFLOW' => null,
        ];
        $originals = [];
        $this->setVariables($variables, $originals);
        foreach ($method_tests as $name => $expected) {
            $this->assertSame($expected, Environment::$name(), "Asserting Environment::$name");
        }
        // Reset the environment variables.
        $this->setVariables($originals);
    }

    /**
     * Set environment variables manually for testing.
     *
     * @param array $variables
     *   The variable values to set keyed by name.
     * @param array|null $originals
     *   If provided will be populated with the original variable values keyed by name.
     */
    protected function setVariables(array $variables, ?array &$originals = null): void
    {
        foreach ($variables as $type => $type_variables) {
            foreach ($type_variables as $name => $value) {
                switch ($type) {
                    case 'ENV':
                        if (isset($originals)) {
                            $originals[$type][$name] = getenv($name) ?: null;
                        }
                        isset($value) ? putenv("$name=$value") : putenv($name);
                        break;

                    case '_SERVER':
                        if (isset($originals)) {
                            $originals[$type][$name] = $_SERVER[$name] ?? null;
                        }
                        $_SERVER[$name] = $value;
                        break;

                    default:
                        if (isset($originals)) {
                            $originals[$type][$name] = $$type[$name] ?: null;
                        }
                        $$type[$name] = $value;
                        break;
                }
            }
        }
    }

    /**
     * Data provider for ::testEnvironment.
     */
    public static function providerEnvironment(): array
    {
        return [
            'default-state' => [
                [],
                [
                    'getEnvironment' => false,
                    'isAcquia' => false,
                    'isCircleCi' => false,
                    'isGitHubWorkflow' => false,
                    'isGitLabCi' => false,
                    'isTugboat' => false,
                    'isPantheon' => false,
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
                    'ENV' => [
                        'ENVIRONMENT' => 'prod',
                    ],
                ],
                [
                    'getEnvironment' => 'prod',
                    'isAcquia' => false,
                    'isCircleCi' => false,
                    'isGitHubWorkflow' => false,
                    'isGitLabCi' => false,
                    'isTugboat' => false,
                    'isPantheon' => false,
                    'isProduction' => true,
                    'isStaging' => false,
                    'isDevelopment' => false,
                    'isPreview' => false,
                    'isCi' => false,
                    'isLocal' => false,
                    'getIndicatorConfig' => [
                        'name' => 'Production',
                        'bg_color' => '#e7131a',
                        'fg_color' => '#ffffff',
                    ],
                ],
            ],
            'pantheon-empty' => [
                [
                    'ENV' => [
                        'PANTHEON_ENVIRONMENT' => '',
                    ],
                ],
                [
                    'getEnvironment' => false,
                    'isAcquia' => false,
                    'isCircleCi' => false,
                    'isGitHubWorkflow' => false,
                    'isGitLabCi' => false,
                    'isTugboat' => false,
                    'isPantheon' => false,
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
                    'ENV' => [
                        'PANTHEON_ENVIRONMENT' => 'live',
                    ],
                    '_SERVER' => [
                        'HTTP_HOST' => 'www.example.com',
                    ],
                ],
                [
                    'getEnvironment' => 'live',
                    'isAcquia' => false,
                    'isCircleCi' => false,
                    'isGitHubWorkflow' => false,
                    'isGitLabCi' => false,
                    'isTugboat' => false,
                    'isPantheon' => true,
                    'isProduction' => true,
                    'isStaging' => false,
                    'isDevelopment' => false,
                    'isPreview' => false,
                    'isMultidev' => false,
                    'isCustomDomain' => true,
                    'isCi' => false,
                    'isLocal' => false,
                    'getIndicatorConfig' => [
                        'name' => 'Production',
                        'bg_color' => '#e7131a',
                        'fg_color' => '#ffffff',
                    ],
                ],
            ],
            'pantheon-test' => [
                [
                    'ENV' => [
                        'PANTHEON_ENVIRONMENT' => 'test',
                    ],
                    '_SERVER' => [
                        'HTTP_HOST' => 'drupal-environment-test.pantheonsite.io',
                    ],
                ],
                [
                    'getEnvironment' => 'test',
                    'isAcquia' => false,
                    'isCircleCi' => false,
                    'isGitHubWorkflow' => false,
                    'isGitLabCi' => false,
                    'isTugboat' => false,
                    'isPantheon' => true,
                    'isProduction' => false,
                    'isStaging' => true,
                    'isDevelopment' => false,
                    'isPreview' => false,
                    'isMultidev' => false,
                    'isCustomDomain' => false,
                    'isCi' => false,
                    'isLocal' => false,
                    'getIndicatorConfig' => [
                        'name' => 'Staging',
                        'bg_color' => '#b85c00',
                        'fg_color' => '#ffffff',
                    ],
                ],
            ],
            'pantheon-dev' => [
                [
                    'ENV' => [
                        'PANTHEON_ENVIRONMENT' => 'dev',
                    ],
                    '_SERVER' => [
                        'HTTP_HOST' => 'drupal-environment-dev.pantheonsite.io',
                    ],
                ],
                [
                    'getEnvironment' => 'dev',
                    'isAcquia' => false,
                    'isCircleCi' => false,
                    'isGitHubWorkflow' => false,
                    'isGitLabCi' => false,
                    'isTugboat' => false,
                    'isPantheon' => true,
                    'isProduction' => false,
                    'isStaging' => false,
                    'isDevelopment' => true,
                    'isPreview' => false,
                    'isMultidev' => false,
                    'isCustomDomain' => false,
                    'isCi' => false,
                    'isLocal' => false,
                    'getIndicatorConfig' => [
                        'name' => 'Development',
                        'bg_color' => '#307b24',
                        'fg_color' => '#ffffff',
                    ],
                ],
            ],
            'pantheon-multidev' => [
                [
                    'ENV' => [
                        'PANTHEON_ENVIRONMENT' => 'pr-1',
                    ],
                    '_SERVER' => [
                        'HTTP_HOST' => 'drupal-environment-multidev-test.pantheonsite.io',
                    ],
                ],
                [
                    'getEnvironment' => 'pr-1',
                    'isAcquia' => false,
                    'isCircleCi' => false,
                    'isGitHubWorkflow' => false,
                    'isGitLabCi' => false,
                    'isTugboat' => false,
                    'isPantheon' => true,
                    'isProduction' => false,
                    'isStaging' => false,
                    'isDevelopment' => false,
                    'isPreview' => true,
                    'isMultidev' => true,
                    'isCustomDomain' => false,
                    'isCi' => false,
                    'isLocal' => false,
                    'getIndicatorConfig' => [
                        'name' => 'Preview',
                        'bg_color' => '#990055',
                        'fg_color' => '#ffffff',
                    ],
                ],
            ],
            'pantheon-ci' => [
                [
                    'ENV' => [
                        'PANTHEON_ENVIRONMENT' => 'ci',
                    ],
                ],
                [
                    'getEnvironment' => 'ci',
                    'isAcquia' => false,
                    'isCircleCi' => false,
                    'isGitHubWorkflow' => false,
                    'isGitLabCi' => false,
                    'isTugboat' => false,
                    'isPantheon' => true,
                    'isProduction' => false,
                    'isStaging' => false,
                    'isDevelopment' => false,
                    'isPreview' => false,
                    'isMultidev' => false,
                    'isCustomDomain' => false,
                    'isCi' => true,
                    'isLocal' => false,
                    'getIndicatorConfig' => null,
                ],
            ],
            'pantheon-local' => [
                [
                    'ENV' => [
                        'PANTHEON_ENVIRONMENT' => 'local',
                    ],
                ],
                [
                    'getEnvironment' => 'local',
                    'isAcquia' => false,
                    'isCircleCi' => false,
                    'isGitHubWorkflow' => false,
                    'isGitLabCi' => false,
                    'isTugboat' => false,
                    'isPantheon' => true,
                    'isProduction' => false,
                    'isStaging' => false,
                    'isDevelopment' => false,
                    'isPreview' => false,
                    'isMultidev' => false,
                    'isCi' => false,
                    'isLocal' => true,
                    'getIndicatorConfig' => [
                        'name' => 'Local',
                        'bg_color' => '#505050',
                        'fg_color' => '#ffffff',
                    ],
                ],
            ],
            'tugboat' => [
                [
                    'ENV' => [
                        'TUGBOAT_PREVIEW_NAME' => 'phpunit',
                    ],
                ],
                [
                    'getEnvironment' => 'phpunit',
                    'isAcquia' => false,
                    'isCircleCi' => false,
                    'isGitHubWorkflow' => false,
                    'isGitLabCi' => false,
                    'isTugboat' => true,
                    'isPantheon' => false,
                    'isProduction' => false,
                    'isStaging' => false,
                    'isDevelopment' => false,
                    'isPreview' => true,
                    'isCi' => false,
                    'isLocal' => false,
                    'getIndicatorConfig' => [
                        'name' => 'Preview',
                        'bg_color' => '#990055',
                        'fg_color' => '#ffffff',
                    ],
                ],
            ],
            'circleci' => [
                [
                    'ENV' => [
                        'CI' => 'true',
                        'CIRCLECI' => 'true',
                    ],
                ],
                [
                    'getEnvironment' => 'ci',
                    'isAcquia' => false,
                    'isCircleCi' => true,
                    'isGitHubWorkflow' => false,
                    'isGitLabCi' => false,
                    'isTugboat' => false,
                    'isPantheon' => false,
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
                    'ENV' => [
                        'CI' => 'true',
                        'GITHUB_WORKFLOW' => 'test',
                    ],
                ],
                [
                    'getEnvironment' => 'ci',
                    'isAcquia' => false,
                    'isCircleCi' => false,
                    'isGitHubWorkflow' => true,
                    'isGitLabCi' => false,
                    'isTugboat' => false,
                    'isPantheon' => false,
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
                    'ENV' => [
                        'CI' => 'true',
                        'GITLAB_CI' => 'true',
                    ],
                ],
                [
                    'getEnvironment' => 'ci',
                    'isAcquia' => false,
                    'isCircleCi' => false,
                    'isGitHubWorkflow' => false,
                    'isGitLabCi' => true,
                    'isTugboat' => false,
                    'isPantheon' => false,
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
                    'ENV' => [
                        'IS_DDEV_PROJECT' => true,
                    ],
                ],
                [
                    'getEnvironment' => false,
                    'isAcquia' => false,
                    'isCircleCi' => false,
                    'isGitHubWorkflow' => false,
                    'isGitLabCi' => false,
                    'isTugboat' => false,
                    'isPantheon' => false,
                    'isProduction' => false,
                    'isStaging' => false,
                    'isDevelopment' => false,
                    'isPreview' => false,
                    'isCi' => false,
                    'isLocal' => true,
                    'getIndicatorConfig' => [
                        'name' => 'Local',
                        'bg_color' => '#505050',
                        'fg_color' => '#ffffff',
                    ],
                ],
            ],
            'lando' => [
                [
                    'ENV' => [
                        'LANDO_INFO' => '[...]',
                    ],
                ],
                [
                    'getEnvironment' => false,
                    'isAcquia' => false,
                    'isCircleCi' => false,
                    'isGitHubWorkflow' => false,
                    'isGitLabCi' => false,
                    'isTugboat' => false,
                    'isPantheon' => false,
                    'isProduction' => false,
                    'isStaging' => false,
                    'isDevelopment' => false,
                    'isPreview' => false,
                    'isCi' => false,
                    'isLocal' => true,
                    'getIndicatorConfig' => [
                        'name' => 'Local',
                        'bg_color' => '#505050',
                        'fg_color' => '#ffffff',
                    ],
                ],
            ],
            'composer' => [
                [
                    'ENV' => [
                        'COMPOSER' => 'alternate.ext',
                    ],
                ],
                [
                    'getComposerFilename' => 'alternate.ext',
                    'getComposerLockFilename' => 'alternate.lock',
                ]
            ]
        ];
    }
}
