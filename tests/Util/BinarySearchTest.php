<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

namespace Vivarium\Collection\Test\Util;

use PHPUnit\Framework\TestCase;
use Vivarium\Collection\Util\BinarySearch;
use Vivarium\Comparator\IntegerComparator;

/**
 * @coversDefaultClass \Vivarium\Collection\Util\BinarySearch
 */
final class BinarySearchTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::search()
     * @covers ::searchRecursive()
     * @covers ::sign()
     */
    public function testSearch(): void
    {
        $binary = new BinarySearch(new IntegerComparator());
        $arr    = [1, 2, 4, 5, 7, 8];

        static::assertSame(0, $binary->search($arr, 1));
        static::assertSame(5, $binary->search($arr, 8));
        static::assertSame(2, $binary->search($arr, 4));
        static::assertSame(-3, $binary->search($arr, 3));
    }

    /**
     * @covers ::__construct()
     * @covers ::contains()
     * @covers ::searchRecursive()
     * @covers ::sign()
     */
    public function testContains(): void
    {
        $binary = new BinarySearch(new IntegerComparator());
        $arr    = [1, 2, 4, 5, 7, 8];

        static::assertTrue($binary->contains($arr, 1));
        static::assertTrue($binary->contains($arr, 8));
        static::assertTrue($binary->contains($arr, 4));
        static::assertFalse($binary->contains($arr, 10));
    }
}
