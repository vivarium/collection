<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Collection\Test\Map;

use OutOfBoundsException;
use PHPUnit\Framework\TestCase;
use stdClass;
use Vivarium\Collection\Map\Map;
use Vivarium\Collection\Map\SortedMap;
use Vivarium\Comparator\IntegerComparator;
use Vivarium\Comparator\StringComparator;

use function count;
use function usort;

/**
 * @coversDefaultClass \Vivarium\Collection\Map\SortedMap
 */
class SortedMapTest extends TestCase
{
    /**
     * @covers ::fromKeyValue()
     * @covers ::keys()
     */
    public function testKeyOrder(): void
    {
        /** @var string[] $keys */
        $keys   = ['c', 'b', 'a', 'd'];
        $values = [1, 2, 3, 4];

        $map = SortedMap::fromKeyValue(new StringComparator(), $keys, $values);

        $expected = ['a', 'b', 'c', 'd'];
        $keys     = $map->keys();

        static::assertEquals($expected, $keys);
    }

    /**
     * @covers ::__construct()
     * @covers ::put()
     * @covers ::keys()
     * @covers ::count()
     * @covers ::searchByPair()
     */
    public function testPut(): void
    {
        /** @var string[] $keys */
        $keys   = ['c', 'b', 'a', 'd'];
        $values = [1, 2, 3, 4];

        /** @var Map<string, int> $map */
        $map = SortedMap::fromKeyValue(new StringComparator(), $keys, $values);

        $map = $map->put('f', 42);
        $map = $map->put('a', 5);

        static::assertCount(5, $map);
        static::assertEquals(5, $map->get('a'));
    }

    /**
     * @covers ::put()
     * @covers ::searchByPair()
     */
    public function testDoublePut(): void
    {
        $map = new SortedMap(new StringComparator());
        $map = $map
            ->put('c', 1)
            ->put('c', 12)
            ->put('a', 7)
            ->put('b', 2);

        $keys   = ['a', 'b', 'c'];
        $values = [7, 2, 12];

        static::assertCount(3, $map);
        static::assertEquals($keys, $map->keys());
        static::assertEquals($values, $map->values());
        static::assertNotSame($map, $map->put('z', 42));
    }

    /**
     * @covers ::keys
     */
    public function testKeySorting(): void
    {
        $map = new SortedMap(new StringComparator());
        $map = $map
            ->put('Hello', 1)
            ->put('H', 1)
            ->put('Ab', 1)
            ->put('A', 1);

        $keys = ['A', 'Ab', 'H', 'Hello'];
        static::assertEquals($keys, $map->keys());
    }

    /**
     * @covers ::get()
     * @covers ::searchByKey()
     */
    public function testGet(): void
    {
        /** @var int[] $keys */
        $keys   = [0, 1];
        $values = ['a', 'b'];

        /** @var Map<int, string> $map */
        $map = SortedMap::fromKeyValue(new IntegerComparator(), $keys, $values);

        static::assertEquals('a', $map->get(0));
        static::assertEquals('b', $map->get(1));
    }

    /**
     * @covers ::get()
     */
    public function testGetNotFound(): void
    {
        static::expectException(OutOfBoundsException::class);
        static::expectExceptionMessage('The provided key is not valid.');

        $map = new SortedMap(new IntegerComparator());

        /** @psalm-suppress UnusedMethodCall */
        $map->get(1);
    }

    /**
     * @covers ::__construct()
     * @covers ::remove()
     * @covers ::containsKey()
     */
    public function testRemove(): void
    {
        /** @var int[] $keys */
        $keys   = [5, 3, 2, 1, 7];
        $values = [7, 5, 3, 8, 9];

        /** @var Map<int, int> $map */
        $map = SortedMap::fromKeyValue(new IntegerComparator(), $keys, $values);

        static::assertTrue($map->containsKey(2));

        $map = $map->remove(2);

        static::assertFalse($map->containsKey(2));
        static::assertNotSame($map, $map->remove(1));
    }

    /**
     * @covers ::put()
     * @covers ::remove()
     */
    public function testRemoveOnFirstElement(): void
    {
        $map = new SortedMap(new IntegerComparator());
        $map = $map
            ->put(0, 1)
            ->put(1, 1);

        static::assertTrue($map->containsKey(0));
        static::assertTrue($map->containsKey(1));
        static::assertFalse($map->containsKey(2));

        $map = $map->remove(0);

        static::assertFalse($map->containsKey(0));
        static::assertFalse($map->containsKey(2));
        static::assertTrue($map->containsKey(1));
    }

    /**
     * @covers ::pairs()
     */
    public function testPairs(): void
    {
        /** @var int[] $keys */
        $keys   = [1, 2, 3, 4, 5];
        $values = ['a', 'b', 'c', 'd', 'e'];

        /** @var Map<int, string> $map */
        $map = SortedMap::fromKeyValue(new IntegerComparator(), $keys, $values);

        usort($keys, new IntegerComparator());

        $pairs = $map->pairs();
        for ($i = 0; $i < count($keys); $i++) {
            static::assertEquals($keys[$i], $pairs[$i]->getKey());
        }
    }

    /**
     * @covers ::clear()
     */
    public function testClear(): void
    {
        /** @var int[] $keys */
        $keys   = [1, 2, 3, 4, 5];
        $values = ['a', 'b', 'c', 'd', 'e'];

        /** @var Map<int, string> $map */
        $map = SortedMap::fromKeyValue(new IntegerComparator(), $keys, $values);
        $map = $map->clear();

        static::assertCount(0, $map);
    }

    /**
     * @covers ::getIterator
     */
    public function testGetIterator(): void
    {
        /** @var string[] $keys */
        $keys   = ['a', 'b', 'c'];
        $values = [1, 2, 3];

        /** @var Map<string, int> $map */
        $map = SortedMap::fromKeyValue(new StringComparator(), $keys, $values);

        $order = [
            'a' => 1,
            'b' => 2,
            'c' => 3,
        ];

        $iterator = $map->getIterator();
        foreach ($order as $key => $value) {
            static::assertEquals($key, $iterator->key());
            static::assertEquals($value, $iterator->current());
            $iterator->next();
        }
    }

    /**
     * @covers ::containsValue()
     */
    public function testContainsValue(): void
    {
        /** @var int[] $keys */
        $keys   = [1, 2, 3, 4, 5];
        $values = ['a', 'b', 'c', 'd', 'e'];

        $map = SortedMap::fromKeyValue(new IntegerComparator(), $keys, $values);

        static::assertTrue($map->containsValue('c'));
        static::assertFalse($map->containsValue('z'));
    }

    /**
     * @covers ::values()
     */
    public function testValues(): void
    {
        /** @var int[] $keys */
        $keys   = [1, 2];
        $values = ['a', 'b'];

        $map = SortedMap::fromKeyValue(new IntegerComparator(), $keys, $values);

        static::assertSame(['a', 'b'], $map->values());
    }

    /**
     * @covers ::isEmpty()
     */
    public function testIsEmpty(): void
    {
        $map = new SortedMap(new IntegerComparator());

        static::assertTrue($map->isEmpty());
    }

    /**
     * @covers ::equals()
     */
    public function testEquality(): void
    {
        /** @var int[] $keys */
        $keys   = [1, 2, 3];
        $values = ['a', 'b', 'c'];

        $map1 = SortedMap::fromKeyValue(new IntegerComparator(), $keys, $values);
        $map2 = SortedMap::fromKeyValue(new IntegerComparator(), $keys, $values);

        /** @var int[] $keys */
        $keys   = [3, 4, 5];
        $values = ['d', 'e', 'f'];

        $map3 = SortedMap::fromKeyValue(new IntegerComparator(), $keys, $values);

        static::assertTrue($map1->equals($map1));
        static::assertTrue($map1->equals($map2));
        static::assertFalse($map1->equals(new stdClass()));
        static::assertFalse($map1->equals($map3));
    }

    /**
     * @covers ::hash()
     */
    public function testHash(): void
    {
        /** @var int[] $keys */
        $keys   = [1, 2, 3];
        $values = ['a', 'b', 'c'];

        $map1 = SortedMap::fromKeyValue(new IntegerComparator(), $keys, $values);
        $map2 = SortedMap::fromKeyValue(new IntegerComparator(), $keys, $values);

        /** @var int[] $keys */
        $keys   = [3, 4, 5];
        $values = ['d', 'e', 'f'];

        $map3 = SortedMap::fromKeyValue(new IntegerComparator(), $keys, $values);

        static::assertSame($map1->hash(), $map1->hash());
        static::assertSame($map1->hash(), $map2->hash());
        static::assertNotSame($map1->hash(), $map3->hash());
    }
}
