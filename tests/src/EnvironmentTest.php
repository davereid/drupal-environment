<?php

declare(strict_types=1);

namespace DrupalEnvironment\Tests;

use DrupalEnvironment\Acquia;
use DrupalEnvironment\DefaultEnvironment;
use DrupalEnvironment\Environment;
use DrupalEnvironment\Pantheon;
use DrupalEnvironment\Tugboat;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Tests the Environment.
 */
final class EnvironmentTest extends TestCase
{
    use SetVariablesTestTrait;

    /**
     * Test the commandExists() method.
     */
    public function testCommandExists(): void
    {
        $this->assertTrue(Environment::commandExists('php'));
        $this->assertFalse(Environment::commandExists('invalid-command'));
    }

    /**
     * Test that commandExists() caches its result until reset().
     */
    public function testCommandExistsCache(): void
    {
        $this->assertTrue(Environment::commandExists('php'));

        // Hide php from the shell: only the cached result can still find it.
        putenv('PATH=/nonexistent');
        $this->assertTrue(Environment::commandExists('php'));

        Environment::reset();
        $this->assertFalse(Environment::commandExists('php'));
    }

    /**
     * Test the getMemoryLimit() and getAvailableMemory() methods.
     */
    #[DataProvider('providerMemoryLimit')]
    public function testMemoryLimit(string $value, int $expected): void
    {
        $original = ini_get('memory_limit');
        try {
            $this->assertNotFalse(ini_set('memory_limit', $value));
            $this->assertSame($expected, Environment::getMemoryLimit());
            $this->assertSame(
                $expected === -1 ? -1 : $expected - memory_get_usage(true),
                Environment::getAvailableMemory()
            );
        } finally {
            ini_set('memory_limit', $original);
        }
    }

    /**
     * Data provider for testMemoryLimit().
     *
     * @return array<string, array{string, int}>
     *   Test cases of a memory limit and its value in bytes.
     */
    public static function providerMemoryLimit(): array
    {
        return [
            'unlimited' => ['-1', -1],
            'bytes' => ['1073741824', 1073741824],
            'kilobytes' => ['1048576K', 1073741824],
            'megabytes' => ['1024M', 1073741824],
            'gigabytes' => ['2G', 2147483648],
            'lowercase' => ['2g', 2147483648],
        ];
    }

    /**
     * Test that isTest() returns false in an isolated standard process.
     */
    public function testIsTestReturnsFalseInStandardEnvironment(): void
    {
        if (!function_exists('shell_exec')) {
            $this->markTestSkipped('shell_exec() is disabled.');
        }

        $autoloadPath = dirname(__DIR__, 2) . '/vendor/autoload.php';
        if (!is_file($autoloadPath)) {
            $this->markTestIncomplete('Could not find autoload.php.');
        }

        $code = sprintf(
            'require %s; echo \DrupalEnvironment\Environment::isTest() ? "true" : "false";',
            var_export($autoloadPath, true)
        );
        $command = sprintf('%s -r %s', escapeshellcmd(PHP_BINARY), escapeshellarg($code));
        $output = shell_exec($command);

        $this->assertSame('false', trim((string) $output));
    }

    /**
     * Test the environment methods.
     *
     * @param array<string, array<string, mixed>> $variables
     *   The variables to set, keyed by type ('ENV' or '_SERVER') and name.
     * @param array<string, mixed> $method_tests
     *   The expected return values, keyed by Environment method name.
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
            // When running under CI or DDEV, we need to ensure these are reset.
            'CI' => null,
            'IS_DDEV_PROJECT' => null,
        ];
        $this->setVariables($variables);
        foreach ($method_tests as $name => $expected) {
            $this->assertSame($expected, Environment::$name(), "Asserting Environment::$name");
        }
    }

    /**
     * Data provider for ::testEnvironment.
     *
     * @return array<string, array{array<string, array<string, mixed>>, array<string, mixed>}>
     *   Test cases of variables to set and the expected method return values.
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
                    'isTugboat' => false,
                    'isPantheon' => false,
                    'isProduction' => false,
                    'isStaging' => false,
                    'isDevelopment' => false,
                    'isPreview' => false,
                    'isCi' => false,
                    'isCli' => (PHP_SAPI === 'cli'),
                    'isLocal' => false,
                    'isTest' => true,
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
                    'isTugboat' => false,
                    'isPantheon' => false,
                    'isProduction' => true,
                    'isStaging' => false,
                    'isDevelopment' => false,
                    'isPreview' => false,
                    'isCi' => false,
                    'isLocal' => false,
                    'isTest' => true,
                    'getIndicatorConfig' => [
                        'name' => 'Production',
                        'bg_color' => '#e7131a',
                        'fg_color' => '#ffffff',
                    ],
                ],
            ],
            'default-prod-app-env' => [
                [
                    'ENV' => [
                        'APP_ENV' => 'prod',
                    ],
                ],
                [
                    'getEnvironmentClass' => DefaultEnvironment::class,
                    'getEnvironment' => 'prod',
                    'isAcquia' => false,
                    'isTugboat' => false,
                    'isPantheon' => false,
                    'isProduction' => true,
                    'isStaging' => false,
                    'isDevelopment' => false,
                    'isPreview' => false,
                    'isCi' => false,
                    'isLocal' => false,
                    'isTest' => true,
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
                    'isTugboat' => false,
                    'isPantheon' => false,
                    'isProduction' => false,
                    'isStaging' => false,
                    'isDevelopment' => true,
                    'isPreview' => false,
                    'isCi' => false,
                    'isLocal' => false,
                    'isTest' => true,
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
                    'isTugboat' => false,
                    'isPantheon' => false,
                    'isProduction' => false,
                    'isStaging' => false,
                    'isDevelopment' => false,
                    'isPreview' => false,
                    'isCi' => false,
                    'isLocal' => false,
                    'isTest' => true,
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
                    'isTest' => true,
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
                    'isTest' => true,
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
                    'isTest' => true,
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
                    'isTest' => true,
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
                    'isTest' => true,
                    'getIndicatorConfig' => [
                        'name' => 'CI',
                        'bg_color' => '#663894',
                        'fg_color' => '#ffffff',
                    ],
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
                    'isTugboat' => false,
                    'isPantheon' => true,
                    'isProduction' => false,
                    'isStaging' => false,
                    'isDevelopment' => false,
                    'isPreview' => false,
                    'isMultidev' => false,
                    'isCi' => false,
                    'isLocal' => true,
                    'isTest' => true,
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
                    'isTugboat' => true,
                    'isPantheon' => false,
                    'isProduction' => false,
                    'isStaging' => false,
                    'isDevelopment' => false,
                    'isPreview' => true,
                    'isCi' => false,
                    'isLocal' => false,
                    'isTest' => true,
                    'getIndicatorConfig' => [
                        'name' => 'Preview',
                        'bg_color' => '#20688C',
                        'fg_color' => '#ffffff',
                    ],
                ],
            ],
            'ci' => [
                [
                    'ENV' => [
                        'CI' => 'true',
                    ],
                ],
                [
                    'getEnvironmentClass' => DefaultEnvironment::class,
                    'getEnvironment' => false,
                    'isAcquia' => false,
                    'isTugboat' => false,
                    'isPantheon' => false,
                    'isProduction' => false,
                    'isStaging' => false,
                    'isDevelopment' => false,
                    'isPreview' => false,
                    'isCi' => true,
                    'isLocal' => false,
                    'isTest' => true,
                    'getIndicatorConfig' => [
                        'name' => 'CI',
                        'bg_color' => '#663894',
                        'fg_color' => '#ffffff',
                    ],
                ],
            ],
            'ci-production' => [
                [
                    'ENV' => [
                        'CI' => 'true',
                        'ENVIRONMENT' => 'prod',
                    ],
                ],
                [
                    'getEnvironmentClass' => DefaultEnvironment::class,
                    'getEnvironment' => 'prod',
                    'isProduction' => true,
                    'isCi' => true,
                    'isLocal' => false,
                ],
            ],
            'ci-ddev' => [
                [
                    'ENV' => [
                        'CI' => 'true',
                        'IS_DDEV_PROJECT' => 'true',
                    ],
                ],
                [
                    'getEnvironmentClass' => DefaultEnvironment::class,
                    'isCi' => true,
                    'isLocal' => true,
                    'isDdev' => true,
                ],
            ],
            'ci-false' => [
                [
                    'ENV' => [
                        'CI' => 'false',
                    ],
                ],
                [
                    'getEnvironmentClass' => DefaultEnvironment::class,
                    'getEnvironment' => false,
                    'isCi' => false,
                ],
            ],
            'ci-false-uppercase' => [
                [
                    'ENV' => [
                        'CI' => 'FALSE',
                    ],
                ],
                [
                    'isCi' => false,
                ],
            ],
            'ci-no' => [
                [
                    'ENV' => [
                        'CI' => 'no',
                    ],
                ],
                [
                    'isCi' => false,
                ],
            ],
            'ci-zero' => [
                [
                    'ENV' => [
                        'CI' => '0',
                    ],
                ],
                [
                    'isCi' => false,
                ],
            ],
            'ci-off' => [
                [
                    'ENV' => [
                        'CI' => 'off',
                    ],
                ],
                [
                    'isCi' => false,
                ],
            ],
            'ci-empty' => [
                [
                    'ENV' => [
                        'CI' => '',
                    ],
                ],
                [
                    'isCi' => false,
                ],
            ],
            'pantheon-multidev-ci' => [
                [
                    'ENV' => [
                        'PANTHEON_ENVIRONMENT' => 'pr-1',
                        'CI' => 'true',
                    ],
                ],
                [
                    'getEnvironmentClass' => Pantheon::class,
                    'isCi' => true,
                    'isMultidev' => false,
                    'isPreview' => false,
                ],
            ],
            'ci-service-name' => [
                [
                    'ENV' => [
                        'CI' => 'woodpecker',
                    ],
                ],
                [
                    'isCi' => true,
                ],
            ],
            'environment-ci' => [
                [
                    'ENV' => [
                        'ENVIRONMENT' => 'ci',
                    ],
                ],
                [
                    'getEnvironmentClass' => DefaultEnvironment::class,
                    'getEnvironment' => 'ci',
                    'isCi' => true,
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
                    'isTugboat' => false,
                    'isPantheon' => false,
                    'isProduction' => false,
                    'isStaging' => false,
                    'isDevelopment' => false,
                    'isPreview' => false,
                    'isCi' => false,
                    'isLocal' => true,
                    'isTest' => true,
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
                    'isTugboat' => false,
                    'isPantheon' => false,
                    'isProduction' => false,
                    'isStaging' => false,
                    'isDevelopment' => false,
                    'isPreview' => false,
                    'isCi' => false,
                    'isLocal' => true,
                    'isTest' => true,
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
                    'isTugboat' => false,
                    'isPantheon' => false,
                    'isProduction' => false,
                    'isStaging' => false,
                    'isDevelopment' => false,
                    'isPreview' => false,
                    'isCi' => false,
                    'isLocal' => false,
                    'isTest' => true,
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
            'current-domain-server-name' => [
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
            'current-domain-server-addr' => [
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
