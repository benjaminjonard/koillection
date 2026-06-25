<?php

declare(strict_types=1);

namespace App\Tests\App\Entity;

use App\Entity\DisplayConfiguration;
use PHPUnit\Framework\TestCase;

class DisplayConfigurationTest extends TestCase
{
    public function test_set_columns_filters_out_null_and_empty_values(): void
    {
        $displayConfiguration = new DisplayConfiguration();
        $displayConfiguration->setColumns(['Author', null, '', 'Editor']);

        $this->assertSame(['Author', 'Editor'], $displayConfiguration->getColumns());
    }

    public function test_get_columns_filters_out_corrupted_values(): void
    {
        // Simulate a row hydrated by Doctrine with corrupted JSON (bypasses the setter)
        $displayConfiguration = new DisplayConfiguration();
        $reflection = new \ReflectionProperty(DisplayConfiguration::class, 'columns');
        $reflection->setValue($displayConfiguration, [null, 'Author', '', null]);

        $this->assertSame(['Author'], $displayConfiguration->getColumns());
    }

    public function test_set_columns_keeps_null(): void
    {
        $displayConfiguration = new DisplayConfiguration();
        $displayConfiguration->setColumns(null);

        $this->assertNull($displayConfiguration->getColumns());
    }
}
