<?php

declare(strict_types=1);

namespace DrupalEnvironment\Tests;

use DrupalEnvironment\Environment;

/**
 * Class that overrides the redirect method for testing.
 */
final class RedirectTestEnvironment extends Environment
{
    /**
     * If the request is supposed to be a CLI request.
     */
    public static bool $isCli = false;

    /**
     * {@inheritdoc}
     */
    public static function isCli(): bool
    {
        return static::$isCli;
    }

    /**
     * {@inheritdoc}
     */
    public static function reset(): void
    {
        parent::reset();
        static::$isCli = false;
    }

    /**
     * {@inheritdoc}
     */
    protected static function redirect(string $url, int $code = 301): never
    {
        throw new \RuntimeException($url, $code);
    }
}
