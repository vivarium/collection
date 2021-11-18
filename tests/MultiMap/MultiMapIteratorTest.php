<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

namespace Vivarium\Collection\Test\MultiMap;

use Vivarium\Collection\Map\MapIterator;
use Vivarium\Collection\MultiMap\MultiMapIterator;
use Vivarium\Collection\Pair\Pair;
use Vivarium\Collection\Sequence\ArraySequence;
use Vivarium\Collection\Test\Iterator\CommonIteratorTest;
use Vivarium\Collection\Test\Stub\Key;
use Vivarium\Equality\HashBuilder;

/**
 * @coversDefaultClass \Vivarium\Collection\MultiMap\MultiMapIterator
 */
final class MultiMapIteratorTest extends CommonIteratorTest
{
    /**
     * @covers ::__construct()
     * @covers ::rewind()
     * @covers ::valid()
     * @covers ::key()
     * @covers ::current()
     * @covers ::next()
     */
    public function testMultiMapIterator(): void
    {
        $map = static function (int $key): Key {
            return new Key($key);
        };

        $values = [
            1 => new ArraySequence(1, 2),
            3 => new ArraySequence(4, 5),
        ];

        $pairs = [];
        foreach ($values as $key => $value) {
            $hash = (new HashBuilder())
                ->append($map($key))
                ->getHashCode();

            $pairs[$hash] = new Pair($map($key), $value);
        }

        $iterator = new MultiMapIterator(
            new MapIterator($pairs)
        );

        $this->doTest($iterator, [1, 1, 3, 3], [1, 2, 4, 5], $map);
    }
}
