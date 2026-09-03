<?php

declare(strict_types=1);

namespace DrupalEnvironment\Tests;

use DrupalEnvironment\Acquia;
use DrupalEnvironment\CircleCi;
use DrupalEnvironment\DefaultEnvironment;
use DrupalEnvironment\Environment;
use DrupalEnvironment\GitHubWorkflow;
use DrupalEnvironment\GitLabCi;
use DrupalEnvironment\Pantheon;
use DrupalEnvironment\Tugboat;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Tests Environment::enforeDomain().
 */
final class EnforceDomainTest extends TestCase
{

    use SetVariablesTrait;

    /**
     * Test that enforceDomain() does not redirect CLI requests.
     */
    public function testEnforceDomainMatches(): void
    {
        $this->setVariables([
            '_SERVER' => [
                'REQUEST_URI' => '/current-path?query=value',
                'HTTP_HOST' => 'example.com',
            ],
        ]);
        RedirectTestEnvironment::enforceDomain('example.com');
        $this->expectNotToPerformAssertions();
    }

    /**
     * Test that enforceDomain() does not redirect CLI requests.
     */
    public function testEnforceDomainSkipsCliRequests(): void
    {
        RedirectTestEnvironment::$isCli = true;
        $this->setVariables([
            '_SERVER' => [
                'REQUEST_URI' => '/current-path?query=value',
                'HTTP_HOST' => 'legacy.example.com',
            ],
        ]);
        RedirectTestEnvironment::enforceDomain('example.com');
        $this->expectNotToPerformAssertions();
    }

    /**
     * Test that enforceDomain() redirects requests using the wrong domain.
     */
    public function testEnforceDomainRedirectsToPreferredDomain(): void
    {
        $this->setVariables([
            '_SERVER' => [
                'REQUEST_URI' => '/current-path?query=value',
                'HTTP_HOST' => 'legacy.example.com',
            ],
        ]);
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('https://example.com/current-path?query=value');
        $this->expectExceptionCode(301);
        RedirectTestEnvironment::enforceDomain('example.com');
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
            'APP_ENV' => null,
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
                        Environment::set($name, $value);
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
                    'getEnvironmentClass' => DefaultEnvironment::class,
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
                    'getEnvironmentClass' => DefaultEnvironment::class,
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
            'default-prod-appenv' => [
                [
                    'ENV' => [
                        'APP_ENV' => 'prod',
                    ],
                ],
                [
                    'getEnvironmentClass' => DefaultEnvironment::class,
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
            'environment-priority' => [
                [
                    'ENV' => [
                        'ENVIRONMENT' => 'dev',
                        'APP_ENV' => 'prod',
                    ],
                ],
                [
                    'getEnvironmentClass' => DefaultEnvironment::class,
                    'getEnvironment' => 'dev',
                    'isAcquia' => false,
                    'isCircleCi' => false,
                    'isGitHubWorkflow' => false,
                    'isGitLabCi' => false,
                    'isTugboat' => false,
                    'isPantheon' => false,
                    'isProduction' => false,
                    'isStaging' => false,
                    'isDevelopment' => true,
                    'isPreview' => false,
                    'isCi' => false,
                    'isLocal' => false,
                    'getIndicatorConfig' => [
                        'name' => 'Development',
                        'bg_color' => '#307b24',
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
                    'getEnvironmentClass' => DefaultEnvironment::class,
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
                        'HTTP_X_FORWARDED_HOST' => 'www.example.com',
                        'HTTP_HOST' => 'drupal-environment-live.pantheonsite.io',
                    ],
                ],
                [
                    'getEnvironmentClass' => Pantheon::class,
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
                    'getHost' => 'www.example.com',
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
                        'HTTP_X_FORWARDED_HOST' => 'drupal-environment-test.pantheonsite.io',
                    ],
                ],
                [
                    'getEnvironmentClass' => Pantheon::class,
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
                    'getHost' => 'drupal-environment-test.pantheonsite.io',
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
                    'getEnvironmentClass' => Pantheon::class,
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
                    'getHost' => 'drupal-environment-dev.pantheonsite.io',
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
                    'getEnvironmentClass' => Pantheon::class,
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
                    'getHost' => 'drupal-environment-multidev-test.pantheonsite.io',
                    'getIndicatorConfig' => [
                        'name' => 'Preview',
                        'bg_color' => '#20688C',
                        'fg_color' => '#ffffff',
                    ],
                ],
            ],
            'pantheon-ci' => [
                [
                    'ENV' => [
                        'PANTHEON_ENVIRONMENT' => 'ci',
                    ],
                    '_SERVER' => [
                        'HTTP_HOST' => 'drupal-environment-multidev-ci.pantheonsite.io',
                    ],
                ],
                [
                    'getEnvironmentClass' => Pantheon::class,
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
                    'getEnvironmentClass' => Pantheon::class,
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
                    'getEnvironmentClass' => Tugboat::class,
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
                        'bg_color' => '#20688C',
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
                    'getEnvironmentClass' => CircleCi::class,
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
                    'getEnvironmentClass' => GitHubWorkflow::class,
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
                    'getEnvironmentClass' => GitLabCi::class,
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
                    'getEnvironmentClass' => DefaultEnvironment::class,
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
                    'getEnvironmentClass' => DefaultEnvironment::class,
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
            'manual-class' => [
                [
                    'ENV' => [
                        'DRUPAL_ENVIRONMENT_CLASS' => Acquia::class,
                    ],
                ],
                [
                    'getEnvironmentClass' => Acquia::class,
                    'getEnvironment' => false,
                    'isAcquia' => true,
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
            ],
            'current-domain-servername' => [
                [
                    '_SERVER' => [
                        'SERVER_NAME' => 'www.SERVERNAME.com:443',
                        'SERVER_ADDR' => '127.0.0.1:80',
                    ],
                ],
                [
                    'getHost' => 'www.servername.com',
                ],
            ],
            'current-domain-serveraddr' => [
                [
                    '_SERVER' => [
                        'HTTP_X_FORWARDED_HOST' => '',
                        'HTTP_HOST' => '',
                        'SERVER_NAME' => '',
                        'SERVER_ADDR' => '127.0.0.1:80',
                    ],
                ],
                [
                    'getHost' => '127.0.0.1',
                ],
            ],
        ];
    }
}
