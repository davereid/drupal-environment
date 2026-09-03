<?php

declare(strict_types=1);

namespace DrupalEnvironment\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Tests Environment::enforeDomain().
 */
final class EnforceDomainTest extends TestCase
{

    use SetVariablesTestTrait;

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
}
