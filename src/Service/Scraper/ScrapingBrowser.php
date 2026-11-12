<?php

declare(strict_types=1);

namespace App\Service\Scraper;

use HeadlessChromium\BrowserFactory;
use HeadlessChromium\Communication\Connection;
use HeadlessChromium\Communication\Message;
use HeadlessChromium\Page;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\HttpClient\NoPrivateNetworkHttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ScrapingBrowser
{
    private const array BINARY_NAMES = ['chromium', 'chromium-browser', 'google-chrome', 'chrome'];

    private const array SKIPPED_REQUEST_HEADERS = ['host', 'accept-encoding', 'content-length', 'connection'];

    private const array SKIPPED_RESPONSE_HEADERS = ['content-encoding', 'transfer-encoding', 'content-length', 'connection'];

    private readonly HttpClientInterface $httpClient;

    public function __construct(
        private readonly ScrapingUrlGuard $urlGuard,
        ?HttpClientInterface $httpClient = null,
        private readonly array $extraFlags = [],
    ) {
        $this->httpClient = $httpClient ?? new NoPrivateNetworkHttpClient(new CurlHttpClient());
    }

    public function isAvailable(): bool
    {
        return $this->getBinary() !== null;
    }

    public function fetch(string $url, array $headers = []): string
    {
        $host = strtolower((string) parse_url($url, \PHP_URL_HOST));
        $browser = new BrowserFactory($this->getBinary())->createBrowser([
            'noSandbox' => true,
            'windowSize' => [1200, 1100],
            'customFlags' => ['--disable-dev-shm-usage', '--disable-gpu', ...$this->extraFlags],
        ]);

        try {
            $page = $browser->createPage();
            $connection = $page->getSession()->getConnection();
            $mainFrameId = $page->getSession()->getTargetId();
            $denied = false;

            $connection->on('method:Fetch.requestPaused', function (array $params) use ($connection, $host, $headers, $mainFrameId, &$denied): void {
                $sameHost = strtolower((string) parse_url($params['request']['url'], \PHP_URL_HOST)) === $host;
                if (!$this->forward($connection, $params, $sameHost ? $headers : []) && ($params['frameId'] ?? null) === $mainFrameId) {
                    $denied = true;
                }
            });
            $connection->sendMessageSync(new Message('Fetch.enable', [
                'patterns' => [['resourceType' => 'Document', 'requestStage' => 'Request']],
            ]));
            $page->getSession()->sendMessage(new Message('Page.navigate', ['url' => $url]));

            $html = $this->waitForStableHtml($page, $connection);

            if ($denied) {
                throw new \RuntimeException(ScrapingUrlGuard::UNREACHABLE_MESSAGE);
            }

            return $html;
        } finally {
            $browser->close();
        }
    }

    private function forward(Connection $connection, array $params, array $extraHeaders): bool
    {
        $request = $params['request'];

        try {
            $this->urlGuard->assertSchemeIsSupported($request['url']);

            $headers = [];
            foreach ([...$request['headers'], ...$extraHeaders] as $name => $value) {
                $name = strtolower((string) $name);
                if (!\in_array($name, self::SKIPPED_REQUEST_HEADERS, true)) {
                    $headers[$name] = $value;
                }
            }

            $response = $this->httpClient->request($request['method'], $request['url'], [
                'headers' => $headers,
                'body' => $this->getRequestBody($request),
                'max_redirects' => 0,
            ]);

            $responseHeaders = [];
            foreach ($response->getHeaders(false) as $name => $values) {
                if (\in_array($name, self::SKIPPED_RESPONSE_HEADERS, true)) {
                    continue;
                }

                foreach ($values as $value) {
                    $responseHeaders[] = ['name' => $name, 'value' => $value];
                }
            }

            $connection->sendMessage(new Message('Fetch.fulfillRequest', [
                'requestId' => $params['requestId'],
                'responseCode' => $response->getStatusCode(),
                'responseHeaders' => $responseHeaders,
                'body' => base64_encode($response->getContent(false)),
            ]));

            return true;
        } catch (\Throwable) {
            $connection->sendMessage(new Message('Fetch.failRequest', [
                'requestId' => $params['requestId'],
                'errorReason' => 'AccessDenied',
            ]));

            return false;
        }
    }

    private function getRequestBody(array $request): string
    {
        if (isset($request['postDataEntries'])) {
            return implode('', array_map(static fn (array $entry): string => base64_decode($entry['bytes'] ?? ''), $request['postDataEntries']));
        }

        return $request['postData'] ?? '';
    }

    private function waitForStableHtml(Page $page, Connection $connection): string
    {
        $html = '';
        $previousHtml = null;
        $deadline = microtime(true) + 10;

        do {
            $wait = microtime(true) + 0.5;
            while (microtime(true) < $wait) {
                if ($connection->waitForData(0.1)) {
                    $connection->readData();
                }
            }

            try {
                $html = (string) $page->evaluate('document.documentElement.outerHTML')->getReturnValue(5000);
            } catch (\Throwable) {
                continue;
            }

            if ($html === $previousHtml) {
                break;
            }

            $previousHtml = $html;
        } while (microtime(true) < $deadline);

        return $html;
    }

    private function getBinary(): ?string
    {
        $directories = explode(\PATH_SEPARATOR, getenv('PATH') ?: '');
        foreach (self::BINARY_NAMES as $name) {
            foreach ($directories as $directory) {
                $path = $directory . \DIRECTORY_SEPARATOR . $name;
                if ($directory !== '' && is_executable($path)) {
                    return $path;
                }
            }
        }

        return null;
    }
}
