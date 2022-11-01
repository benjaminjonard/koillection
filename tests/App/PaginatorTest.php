<?php

declare(strict_types=1);

namespace App\Tests\App;

use App\Model\Paginator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PaginatorTest extends WebTestCase
{
    public function test_paginator_middle_page(): void
    {
        // Arrange
        $paginator = new Paginator(200, 20, 5, '/tags');

        // Act

        // Assert
        $this->assertSame([
            ['num' => 1, 'url' => '/tags?page=1', 'isCurrent' => false],  // first page
            ['num' => '...', 'url' => null, 'isCurrent' => false],
            ['num' => 4, 'url' => '/tags?page=4', 'isCurrent' => false],  // previous page
            ['num' => 5, 'url' => '/tags?page=5', 'isCurrent' => true],   // current page
            ['num' => 6, 'url' => '/tags?page=6', 'isCurrent' => false],  // next page
            ['num' => '...', 'url' => null, 'isCurrent' => false],
            ['num' => 10, 'url' => '/tags?page=10', 'isCurrent' => false], // last page
        ], $paginator->getPages());
        $this->assertSame(5, $paginator->getMaxPagesToShow());
        $this->assertSame(5, $paginator->getCurrentPage());
        $this->assertSame(20, $paginator->getItemsPerPage());
        $this->assertSame(200, $paginator->getTotalItems());
        $this->assertSame(10, $paginator->getNumPages());
        $this->assertSame(6, $paginator->getNextPage());
        $this->assertSame(4, $paginator->getPrevPage());
        $this->assertSame('/tags?page=6', $paginator->getNextUrl());
        $this->assertSame('/tags?page=4', $paginator->getPrevUrl());
    }

    public function test_paginator_first_page(): void
    {
        // Arrange
        $paginator = new Paginator(200, 20, 1, '/tags');

        // Act

        // Assert
        $this->assertSame([
            ['num' => 1, 'url' => '/tags?page=1', 'isCurrent' => true],
            ['num' => 2, 'url' => '/tags?page=2', 'isCurrent' => false],
            ['num' => 3, 'url' => '/tags?page=3', 'isCurrent' => false],
            ['num' => 4, 'url' => '/tags?page=4', 'isCurrent' => false],
            ['num' => '...', 'url' => null, 'isCurrent' => false],
            ['num' => 10, 'url' => '/tags?page=10', 'isCurrent' => false],
        ], $paginator->getPages());
        $this->assertSame(5, $paginator->getMaxPagesToShow());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(20, $paginator->getItemsPerPage());
        $this->assertSame(200, $paginator->getTotalItems());
        $this->assertSame(10, $paginator->getNumPages());
        $this->assertSame(2, $paginator->getNextPage());
        $this->assertEquals(null, $paginator->getPrevPage());
        $this->assertSame('/tags?page=2', $paginator->getNextUrl());
        $this->assertEquals(null, $paginator->getPrevUrl());
    }

    public function test_paginator_last_page(): void
    {
        // Arrange
        $paginator = new Paginator(200, 20, 10, '/tags');

        // Act

        // Assert
        $this->assertSame([
            ['num' => 1, 'url' => '/tags?page=1', 'isCurrent' => false],
            ['num' => '...', 'url' => null, 'isCurrent' => false],
            ['num' => 7, 'url' => '/tags?page=7', 'isCurrent' => false],
            ['num' => 8, 'url' => '/tags?page=8', 'isCurrent' => false],
            ['num' => 9, 'url' => '/tags?page=9', 'isCurrent' => false],
            ['num' => 10, 'url' => '/tags?page=10', 'isCurrent' => true],
        ], $paginator->getPages());
        $this->assertSame(5, $paginator->getMaxPagesToShow());
        $this->assertSame(10, $paginator->getCurrentPage());
        $this->assertSame(20, $paginator->getItemsPerPage());
        $this->assertSame(200, $paginator->getTotalItems());
        $this->assertSame(10, $paginator->getNumPages());
        $this->assertEquals(null, $paginator->getNextPage());
        $this->assertSame(9, $paginator->getPrevPage());
        $this->assertEquals(null, $paginator->getNextUrl());
        $this->assertSame('/tags?page=9', $paginator->getPrevUrl());
    }

    public function test_paginator_only_one_page(): void
    {
        // Arrange
        $paginator = new Paginator(3, 20, 1, '/tags');
        $this->assertSame([], $paginator->getPages());
        $this->assertSame(5, $paginator->getMaxPagesToShow());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(20, $paginator->getItemsPerPage());
        $this->assertSame(3, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getNumPages());
        $this->assertEquals(null, $paginator->getNextPage());
        $this->assertEquals(null, $paginator->getPrevPage());
        $this->assertEquals(null, $paginator->getNextUrl());
        $this->assertEquals(null, $paginator->getPrevUrl());
    }

    public function test_paginator_small_number_of_pages(): void
    {
        // Arrange
        $paginator = new Paginator(25, 20, 1, '/tags');
        $this->assertSame([
            ['num' => 1, 'url' => '/tags?page=1', 'isCurrent' => true],
            ['num' => 2, 'url' => '/tags?page=2', 'isCurrent' => false],
        ], $paginator->getPages());
        $this->assertSame(5, $paginator->getMaxPagesToShow());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(20, $paginator->getItemsPerPage());
        $this->assertSame(25, $paginator->getTotalItems());
        $this->assertSame(2, $paginator->getNumPages());
        $this->assertSame(2, $paginator->getNextPage());
        $this->assertEquals(null, $paginator->getPrevPage());
        $this->assertSame('/tags?page=2', $paginator->getNextUrl());
        $this->assertEquals(null, $paginator->getPrevUrl());
    }

    public function test_paginator_current_page_near_end(): void
    {
        // Arrange
        $paginator = new Paginator(200, 20, 9, '/tags');
        $this->assertSame([
            ['num' => 1, 'url' => '/tags?page=1', 'isCurrent' => false],
            ['num' => '...', 'url' => null, 'isCurrent' => false],
            ['num' => 8, 'url' => '/tags?page=8', 'isCurrent' => false],
            ['num' => 9, 'url' => '/tags?page=9', 'isCurrent' => true],
            ['num' => 10, 'url' => '/tags?page=10', 'isCurrent' => false],
        ], $paginator->getPages());
        $this->assertSame(5, $paginator->getMaxPagesToShow());
        $this->assertSame(9, $paginator->getCurrentPage());
        $this->assertSame(20, $paginator->getItemsPerPage());
        $this->assertSame(200, $paginator->getTotalItems());
        $this->assertSame(10, $paginator->getNumPages());
        $this->assertSame(10, $paginator->getNextPage());
        $this->assertSame(8, $paginator->getPrevPage());
        $this->assertSame('/tags?page=10', $paginator->getNextUrl());
        $this->assertSame('/tags?page=8', $paginator->getPrevUrl());
    }
}
