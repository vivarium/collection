<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Collection\Test\Pair;

use PHPUnit\Framework\TestCase;
use stdClass;
use Vivarium\Collection\Pair\Pair;

/**
 * @coversDefaultClass \Vivarium\Collection\Pair\Pair
 */
class PairTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::getKey()
     * @covers ::getValue()
     */
    public function testGet(): void
    {
        $pair = new Pair('a', 1);

        static::assertEquals('a', $pair->getKey());
        static::assertEquals(1, $pair->getValue());
    }

    /**
     * @covers ::equals()
     */
    public function testEquals(): void
    {
        $pair1 = new Pair('a', 1);
        $pair2 = new Pair('a', 1);
        $pair3 = new Pair('b', 2);

        static::assertTrue($pair1->equals($pair1));
        static::assertTrue($pair1->equals($pair2));
        static::assertFalse($pair1->equals($pair3));
        static::assertFalse($pair1->equals(new stdClass()));
    }

    /**
     * @covers ::hash()
     */
    public function testHash(): void
    {
        $pair = new Pair('a', 1);

        static::assertEquals('7b48edecef9b6487b09facffac1912fdc88c4cbe', $pair->hash());
    }
}
