<?php

declare(strict_types=1);

namespace App\Tests\App\Scraper;

use App\Service\Scraper\ScrapingBrowser;
use App\Service\Scraper\ScrapingUrlGuard;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\HttpClient\NoPrivateNetworkHttpClient;
use Symfony\Component\Process\Process;

class ScrapingBrowserTest extends TestCase
{
    private const array PRIVATE_SUBNETS_EXCEPT_LOOPBACK = [
        '10.0.0.0/8', '172.16.0.0/12', '192.168.0.0/16', '169.254.0.0/16', 'fc00::/7', 'fe80::/10',
    ];

    private static string $directory;

    private static string $publicOrigin;

    private static string $internalOrigin;

    private static array $servers = [];

    private ScrapingBrowser $browser;

    #[\Override]
    public static function setUpBeforeClass(): void
    {
        $internalIp = gethostbyname(gethostname());
        if (!filter_var($internalIp, \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV4) || filter_var($internalIp, \FILTER_VALIDATE_IP, \FILTER_FLAG_NO_PRIV_RANGE) !== false) {
            return;
        }

        self::$directory = sys_get_temp_dir() . '/koillection-scraping-browser-' . bin2hex(random_bytes(4));
        mkdir(self::$directory);

        $publicPort = self::findFreePort();
        $internalPort = self::findFreePort();
        self::$publicOrigin = "http://127.0.0.1:{$publicPort}";
        self::$internalOrigin = "http://{$internalIp}:{$internalPort}";

        file_put_contents(self::$directory . '/internal.php', <<<'PHP'
            <?php
            file_put_contents(__DIR__ . '/hits.log', $_SERVER['REQUEST_METHOD'] . ' ' . $_SERVER['REQUEST_URI'] . "\n", FILE_APPEND);
            echo '<title>internal</title>';
            PHP);

        $internalOrigin = var_export(self::$internalOrigin, true);
        $otherHostOrigin = var_export("http://localhost:{$publicPort}", true);
        file_put_contents(self::$directory . '/public.php', <<<PHP
            <?php
            \$target = {$internalOrigin};
            \$otherHost = {$otherHostOrigin};
            header('Content-Type: text/html');
            switch (parse_url(\$_SERVER['REQUEST_URI'], PHP_URL_PATH)) {
                case '/redirect': header("Location: \$target/redirect", true, 302); exit;
                case '/meta': echo "<meta http-equiv=refresh content=\"0;url=\$target/meta\">"; exit;
                case '/javascript': echo "<script>location.href = '\$target/javascript'</script>"; exit;
                case '/form': echo "<form id=f method=post action=\"\$target/form\"><input name=a value=b></form><script>f.submit()</script>"; exit;
                case '/popup': echo "<script>window.open('\$target/popup')</script>"; exit;
                case '/popup-link': echo "<a id=a target=_blank href=\"\$target/popup-link\">link</a><script>a.click()</script>"; exit;
                case '/popup-form': echo "<form id=f target=_blank method=post action=\"\$target/popup-form\"></form><script>f.submit()</script>"; exit;
                case '/image': echo "<title>image</title><img src=\"\$target/image\">"; exit;
                case '/iframe': echo "<title>iframe</title><iframe src=\"\$target/iframe\"></iframe>"; exit;
                case '/public-redirect': header('Location: /landing', true, 302); exit;
                case '/landing': echo '<title>landing</title>'; exit;
                case '/headers': echo '<title>' . htmlspecialchars(\$_SERVER['HTTP_X_KOILLECTION_TEST'] ?? 'none') . '</title>'; exit;
                case '/to-other-host': header("Location: \$otherHost/headers", true, 302); exit;
                case '/rendered': echo "<title>rendered</title><div id=app></div><script>document.getElementById('app').textContent = 'rendered by javascript'</script>"; exit;
            }
            PHP);

        self::$servers = [
            self::startServer("127.0.0.1:{$publicPort}", 'public.php'),
            self::startServer("0.0.0.0:{$internalPort}", 'internal.php'),
        ];
    }

    #[\Override]
    public static function tearDownAfterClass(): void
    {
        foreach (self::$servers as $server) {
            $server->stop();
        }

        if (isset(self::$directory)) {
            array_map(unlink(...), glob(self::$directory . '/*'));
            rmdir(self::$directory);
        }
    }

    #[\Override]
    protected function setUp(): void
    {
        $this->browser = new ScrapingBrowser(
            new ScrapingUrlGuard(),
            new NoPrivateNetworkHttpClient(new CurlHttpClient(), self::PRIVATE_SUBNETS_EXCEPT_LOOPBACK),
            ['--ip-address-space-overrides=' . substr(self::$publicOrigin ?? '', 7) . '=public']
        );

        if (!isset(self::$directory) || !$this->browser->isAvailable()) {
            $this->markTestSkipped('Chromium or a private network address is not available');
        }

        @unlink(self::$directory . '/hits.log');
    }

    public static function navigationsToPrivateNetwork(): iterable
    {
        yield ['/redirect'];
        yield ['/meta'];
        yield ['/javascript'];
        yield ['/form'];
        yield ['/popup'];
        yield ['/popup-link'];
        yield ['/popup-form'];
    }

    #[DataProvider('navigationsToPrivateNetwork')]
    public function test_navigation_to_private_network_is_blocked(string $path): void
    {
        // Act
        $exception = null;
        try {
            $this->browser->fetch(self::$publicOrigin . $path);
        } catch (\RuntimeException $e) {
            $exception = $e;
        }

        // Assert
        $this->assertNotNull($exception);
        $this->assertSame(ScrapingUrlGuard::UNREACHABLE_MESSAGE, $exception->getMessage());
        $this->assertSame([], $this->getInternalHits());
    }

    public static function subresourcesToPrivateNetwork(): iterable
    {
        yield ['/image'];
        yield ['/iframe'];
    }

    #[DataProvider('subresourcesToPrivateNetwork')]
    public function test_subresource_to_private_network_is_blocked(string $path): void
    {
        // Act
        try {
            $this->browser->fetch(self::$publicOrigin . $path);
        } catch (\RuntimeException) {
        }

        // Assert
        $this->assertSame([], $this->getInternalHits());
    }

    public function test_redirect_to_public_address_is_followed(): void
    {
        // Act
        $html = $this->browser->fetch(self::$publicOrigin . '/public-redirect');

        // Assert
        $this->assertStringContainsString('<title>landing</title>', $html);
    }

    public function test_custom_headers_are_sent_to_scraped_host(): void
    {
        // Act
        $html = $this->browser->fetch(self::$publicOrigin . '/headers', ['X-Koillection-Test' => 'secret']);

        // Assert
        $this->assertStringContainsString('<title>secret</title>', $html);
    }

    public function test_custom_headers_are_not_sent_to_other_host(): void
    {
        // Act
        $html = $this->browser->fetch(self::$publicOrigin . '/to-other-host', ['X-Koillection-Test' => 'secret']);

        // Assert
        $this->assertStringContainsString('<title>none</title>', $html);
    }

    public function test_javascript_is_rendered(): void
    {
        // Act
        $html = $this->browser->fetch(self::$publicOrigin . '/rendered');

        // Assert
        $this->assertStringContainsString('rendered by javascript', $html);
    }

    private function getInternalHits(): array
    {
        $file = self::$directory . '/hits.log';

        return is_file($file) ? file($file, \FILE_IGNORE_NEW_LINES) : [];
    }

    private static function startServer(string $address, string $router): Process
    {
        $process = new Process([\PHP_BINARY, '-S', $address, $router], self::$directory);
        $process->start();

        [$host, $port] = explode(':', $address);
        $host = $host === '0.0.0.0' ? '127.0.0.1' : $host;
        for ($i = 0; $i < 50; ++$i) {
            if ($socket = @fsockopen($host, (int) $port, $errno, $errstr, 0.1)) {
                fclose($socket);

                return $process;
            }
            usleep(100_000);
        }

        throw new \RuntimeException("Unable to start test server on {$address}");
    }

    private static function findFreePort(): int
    {
        $socket = stream_socket_server('tcp://127.0.0.1:0');
        $port = (int) substr(strrchr(stream_socket_get_name($socket, false), ':'), 1);
        fclose($socket);

        return $port;
    }
}
