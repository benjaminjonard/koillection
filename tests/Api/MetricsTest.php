<?php

declare(strict_types=1);

namespace App\Tests\Api;

use App\Enum\ConfigurationEnum;
use App\Tests\ApiTestCase;
use App\Tests\Factory\ConfigurationFactory;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class MetricsTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;

    public function test_can_see_metrics(): void
    {
        // Arrange
        $configurationMetrics = ConfigurationFactory::find(['label' => ConfigurationEnum::ENABLE_METRICS]);
        $configurationMetrics->setValue('true');
        $configurationMetrics->save();

        // Act
        static::createClient()->request('GET', '/api/metrics');

        // Assert
        $this->assertResponseIsSuccessful();
    }

    public function test_cant_see_metrics_if_not_enabled(): void
    {
        // Arrange
        $configurationMetrics = ConfigurationFactory::find(['label' => ConfigurationEnum::ENABLE_METRICS]);
        $configurationMetrics->setValue('test');
        $configurationMetrics->save();

        // Act
        static::createClient()->request('GET', '/api/metrics');

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
