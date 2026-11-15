<?php

declare(strict_types=1);

namespace DrupalEnvironment\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Tests Environment::enforceDomain().
 */
final class EnforceDomainTest extends TestCase
{
    use SetVariablesTestTrait;

    /**
     * Test that enforceDomain() does not redirect when the domain matches.
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
        // Caught by hand: PHPUnit 13 deprecates expectExceptionMessage(), and its
        // replacement doesn't exist in PHPUnit 10-12.
        try {
            RedirectTestEnvironment::enforceDomain('example.com');
            $this->fail('enforceDomain() did not redirect.');
        } catch (\RuntimeException $e) {
            $this->assertSame('https://example.com/current-path?query=value', $e->getMessage());
            $this->assertSame(301, $e->getCode());
        }
    }
}
