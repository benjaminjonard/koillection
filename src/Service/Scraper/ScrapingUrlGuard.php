<?php

declare(strict_types=1);

namespace App\Service\Scraper;

use Symfony\Component\HttpFoundation\IpUtils;

final class ScrapingUrlGuard
{
    public const array ALLOWED_SCHEMES = ['http', 'https'];

    public function assertSchemeIsSupported(?string $url): void
    {
        $scheme = strtolower((string) parse_url((string) $url, \PHP_URL_SCHEME));

        if (!\in_array($scheme, self::ALLOWED_SCHEMES, true)) {
            throw new \RuntimeException('Scraping error: only http and https URLs can be scraped');
        }
    }

    public function resolvePublicAddress(?string $url): string
    {
        $host = parse_url((string) $url, \PHP_URL_HOST);
        if (!\is_string($host) || $host === '') {
            throw new \RuntimeException('Scraping error: the URL has no host to scrape');
        }

        $host = trim($host, '[]');
        $addresses = filter_var($host, \FILTER_VALIDATE_IP) ? [$host] : $this->resolve($host);

        foreach ($addresses as $address) {
            if (IpUtils::isPrivateIp($address)) {
                throw new \RuntimeException('Scraping error: this URL points to a private network address');
            }
        }

        return $addresses[0];
    }

    private function resolve(string $host): array
    {
        if ($addresses = gethostbynamel($host)) {
            return $addresses;
        }

        if ($addresses = array_column(dns_get_record($host, \DNS_AAAA) ?: [], 'ipv6')) {
            return $addresses;
        }

        throw new \RuntimeException("Scraping error: unable to resolve {$host}");
    }
}
