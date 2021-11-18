<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Collection\Test\Set;

use Vivarium\Collection\Pair\Pair;
use Vivarium\Collection\Set\SetIterator;
use Vivarium\Collection\Test\Iterator\CommonIteratorTest;

/**
 * @coversDefaultClass \Vivarium\Collection\Set\SetIterator
 */
class SetIteratorTest extends CommonIteratorTest
{
    /**
     * @covers ::__construct()
     * @covers ::rewind()
     * @covers ::valid()
     * @covers ::key()
     * @covers ::current()
     * @covers ::next()
     */
    public function testSetIterator(): void
    {
        $values = [
            1 => 2,
            3 => 4,
            5 => 6,
            7 => 8,
        ];

        $pairs = [];
        foreach ($values as $key => $value) {
            $pairs[] = new Pair($key, $value);
        }

        $iterator = new SetIterator($pairs);

        $this->doTest($iterator, [0, 1, 2, 3], [1, 3, 5, 7], static fn (int $key): int => $key);
    }
}
