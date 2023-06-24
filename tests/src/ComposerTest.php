<?php

namespace Davereid\DrupalEnvironment\Tests;

use Davereid\DrupalEnvironment\Environment;
use PHPUnit\Framework\TestCase;

/**
 * Tests the Environment helper.
 *
 * @coversDefaultClass \Davereid\DrupalEnvironment\Environment
 * @group helper
 */
final class ComposerTest extends TestCase
{

    /**
     * Test the composer filename static methods.
     *
     * @covers ::getComposerFilename
     * @covers ::getComposerLockFilename
     */
    public function testComposerFilenames(): void {
        $original = getenv('COMPOSER');
        putenv('COMPOSER');

        $this->assertSame('composer.json', Environment::getComposerFilename());
        $this->assertSame('composer.lock', Environment::getComposerLockFilename());

        putenv('COMPOSER=alternate.ext');
        $this->assertSame('alternate.ext', Environment::getComposerFilename());
        $this->assertSame('alternate.lock', Environment::getComposerLockFilename());

        // Reset the environment back.
        putenv('COMPOSER=' . $original);
    }
}
