<?php

declare(strict_types=1);

namespace App\Tests\App\Scraper;

use App\Service\Scraper\ScrapingUrlGuard;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ScrapingUrlGuardTest extends TestCase
{
    private ScrapingUrlGuard $guard;

    #[\Override]
    protected function setUp(): void
    {
        $this->guard = new ScrapingUrlGuard();
    }

    public static function unsupportedSchemes(): iterable
    {
        yield ['file:///etc/passwd'];
        yield ['file:/etc/passwd'];
        yield ['FILE:///etc/passwd'];
        yield ['gopher://127.0.0.1:11211/'];
        yield ['ftp://example.com/archive'];
        yield ['data:text/html,<h1>x</h1>'];
        yield ['about:blank'];
        yield [''];
        yield [null];
    }

    public static function supportedSchemes(): iterable
    {
        yield ['http://example.com/item'];
        yield ['https://example.com/item'];
        yield ['HTTPS://example.com/item'];
    }

    public static function privateAddresses(): iterable
    {
        yield ['http://127.0.0.1:8000/'];
        yield ['http://169.254.169.254/latest/meta-data/'];
        yield ['http://192.168.1.1/'];
        yield ['http://10.0.0.5/'];
        yield ['http://172.16.3.4/'];
        yield ['http://0.0.0.0/'];
        yield ['http://[::1]/'];
        yield ['http://localhost:9000/'];
    }

    #[DataProvider('unsupportedSchemes')]
    public function test_cannot_scrape_an_unsupported_scheme(?string $url): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('only http and https URLs can be scraped');

        $this->guard->assertSchemeIsSupported($url);
    }

    #[DataProvider('supportedSchemes')]
    public function test_can_scrape_http_and_https(string $url): void
    {
        $this->guard->assertSchemeIsSupported($url);

        $this->addToAssertionCount(1);
    }

    #[DataProvider('privateAddresses')]
    public function test_cannot_scrape_a_private_address(string $url): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('private network address');

        $this->guard->resolvePublicAddress($url);
    }

    public function test_can_scrape_a_public_address(): void
    {
        $this->assertSame('1.1.1.1', $this->guard->resolvePublicAddress('http://1.1.1.1/item'));
    }

    public function test_cannot_scrape_an_url_without_host(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('no host to scrape');

        $this->guard->resolvePublicAddress('http:///item');
    }

    public function test_cannot_scrape_an_unresolvable_host(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('unable to resolve');

        $this->guard->resolvePublicAddress('http://koillection-does-not-resolve.invalid/');
    }
}
