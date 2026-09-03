<?php

declare(strict_types=1);

namespace DrupalEnvironment\Tests;

use DrupalEnvironment\Environment;

/**
 * Trait for tests for overriding variables.
 */
trait SetVariablesTestTrait
{

    /**
     * The original variables from before the test run.
     *
     * @var array
     */
    protected static $originalVariables = [];

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        static::$originalVariables = [
            'ENV' => getenv(),
            '_SERVER' => $_SERVER,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->setVariables(static::$originalVariables);
        // Reset all static variables.
        Environment::reset();
    }

    /**
     * Set environment variables manually for testing.
     *
     * @param array $variables
     *   The variable values to set keyed by name.
     */
    protected function setVariables(array $variables): void
    {
        foreach ($variables as $type => $type_variables) {
            foreach ($type_variables as $name => $value) {
                switch ($type) {
                    case 'ENV':
                        isset($value) ? putenv($name . '=' . $value) : putenv($name);
                        break;

                    case '_SERVER':
                        $_SERVER[$name] = $value;
                        break;

                    default:
                        static::$originalVariables[$type][$name] = $$type[$name] ?: null;
                        $$type[$name] = $value;
                        break;
                }
            }
        }
    }
}
