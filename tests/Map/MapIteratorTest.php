<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Collection\Test\Map;

use Vivarium\Collection\Map\MapIterator;
use Vivarium\Collection\Pair\Pair;
use Vivarium\Collection\Test\Iterator\CommonIteratorTest;
use Vivarium\Collection\Test\Stub\Key;
use Vivarium\Collection\Test\Stub\KeyWithHashCollision;
use Vivarium\Equality\HashBuilder;

use function array_keys;
use function array_values;

/**
 * @coversDefaultClass \Vivarium\Collection\Map\MapIterator
 */
class MapIteratorTest extends CommonIteratorTest
{
    /**
     * @covers ::__construct()
     * @covers ::rewind()
     * @covers ::valid()
     * @covers ::key()
     * @covers ::current()
     * @covers ::next()
     */
    public function testMapIterator(): void
    {
        $map = static function (int $key): Key {
            return new Key($key);
        };

        $expected = [
            1 => 2,
            3 => 4,
            5 => 6,
            7 => 8,
        ];

        $pairs = [];
        foreach ($expected as $key => $value) {
            $hash = (new HashBuilder())
                ->append($map($key))
                ->getHashCode();

            $pairs[$hash] = new Pair($map($key), $value);
        }

        $iterator = new MapIterator($pairs);

        $this->doTest($iterator, array_keys($expected), array_values($expected), $map);
    }

    /**
     * @covers ::__construct()
     * @covers ::rewind()
     * @covers ::valid()
     * @covers ::key()
     * @covers ::current()
     * @covers ::next()
     */
    public function testMapIteratorWithBuckets(): void
    {
        $map = static function (int $key): KeyWithHashCollision {
            return new KeyWithHashCollision($key);
        };

        $expected = [
            1 => 2,
            3 => 4,
            5 => 6,
            7 => 8,
            2 => 7,
            4 => 5,
            9 => 9,
            10 => 11,
        ];

        $pairs = [];
        foreach ($expected as $key => $value) {
            $hash = (new HashBuilder())
                ->append($map($key))
                ->getHashCode();

            $pair = new Pair(
                $map($key),
                $value
            );

            if (! isset($pairs[$hash])) {
                $pairs[$hash] = [];
            }

            $pairs[$hash][] = $pair;
        }

        $iterator = new MapIterator($pairs);

        $this->doTest($iterator, array_keys($expected), array_values($expected), $map);
    }
}
