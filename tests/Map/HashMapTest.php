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
use Vivarium\Collection\Map\HashMap;
use Vivarium\Collection\Map\Map;
use Vivarium\Collection\Pair\Pair;
use Vivarium\Collection\Set\HashSet;
use Vivarium\Collection\Test\Stub\KeyWithHashCollision;
use Vivarium\Equality\Equal;
use Vivarium\Equality\Equality;

use function count;
use function rand;

/**
 * @coversDefaultClass \Vivarium\Collection\Map\HashMap
 */
class HashMapTest extends TestCase
{
    /**
     * @covers ::__construct()
     */
    public function testEmptyConstructor(): void
    {
        $set = new HashSet();

        static::assertCount(0, $set);
    }

    /**
     * @covers ::__construct()
     */
    public function testConstructionWithPairs(): void
    {
        $map = new HashMap(
            new Pair('a', 1),
            new Pair('b', 2),
            new Pair('c', 3)
        );

        static::assertCount(3, $map);
    }

    /**
     * @covers ::put()
     * @covers ::count()
     * @covers ::searchByKey()
     */
    public function testPut(): void
    {
        /** @var Map<int, string> $map */
        $map = new HashMap(
            new Pair(1, 'a'),
            new Pair(2, 'b'),
            new Pair(3, 'c')
        );

        $map = $map->put(4, 'd');
        $map = $map->put(1, 'z');

        static::assertCount(4, $map);
        static::assertEquals('z', $map->get(1));
        static::assertNotSame($map, $map->put(5, 'e'));
    }

    /**
     * @covers ::put()
     * @covers ::count()
     * @covers ::searchByPair()
     */
    public function testPutHashCollision(): void
    {
        /** @var Map<KeyWithHashCollision, string> $map */
        $map = new HashMap(
            new Pair(new KeyWithHashCollision(1), 'a'),
            new Pair(new KeyWithHashCollision(1), 'b'),
            new Pair(new KeyWithHashCollision(2), 'c'),
        );

        $map = $map->put(new KeyWithHashCollision(2), 'd');
        $map = $map->put(new KeyWithHashCollision(3), 'z');

        static::assertCount(3, $map);
        static::assertEquals('z', $map->get(new KeyWithHashCollision(3)));
    }

    /**
     * @covers ::get()
     */
    public function testGet(): void
    {
        $keys   = [1, 2, 3, 4];
        $values = ['a', 'b', 'c', 'd'];

        $map   = HashMap::fromKeyValue($keys, $values);
        $index = rand(0, count($keys) - 1);

        $value = $map->get($keys[$index]);

        static::assertEquals($values[$index], $value);
    }

    /**
     * @covers ::get()
     */
    public function testGetWithEquality(): void
    {
        $equality = $this->createMock(Equality::class);
        $equality->method('hash')
                 ->willReturn('a4bbe8f088dbfba72434f05585ed0ce4ecb3c952');

        $equality->method('equals')
                 ->willReturnMap([
                     [$equality, true],
                 ]);

        $map   = new HashMap(new Pair($equality, 1));
        $value = $map->get($equality);

        static::assertEquals(1, $value);
    }

    /**
     * @covers ::get()
     */
    public function testGetException(): void
    {
        static::expectException(OutOfBoundsException::class);
        static::expectExceptionMessage('The provided key is not present.');

        $map = new HashMap();

        /** @psalm-suppress UnusedMethodCall  */
        $map->get(new stdClass());
    }

    /**
     * @covers ::remove()
     * @covers ::containsKey()
     * @covers ::count()
     */
    public function testRemove(): void
    {
        $keys   = [1, 2, 3, 4];
        $values = ['a', 'b', 'c', 'd'];

        $map = HashMap::fromKeyValue($keys, $values);
        $map = $map->remove(1)
                   ->remove(2);

        static::assertCount(2, $map);
        static::assertFalse($map->containsKey(1));
        static::assertFalse($map->containsKey(2));
        static::assertNotSame($map, $map->remove(3));
        static::assertSame($map, $map->remove(42));
    }

    /**
     * @covers ::remove()
     * @covers ::count()
     * @covers ::searchByKey()
     */
    public function testRemoveHashCollision(): void
    {
        /** @var Map<KeyWithHashCollision, string> $map */
        $map = new HashMap(
            new Pair(new KeyWithHashCollision(1), 'a'),
            new Pair(new KeyWithHashCollision(1), 'b'),
            new Pair(new KeyWithHashCollision(2), 'c'),
            new Pair(new KeyWithHashCollision(2), 'd'),
            new Pair(new KeyWithHashCollision(3), 'z')
        );

        $map = $map->remove(new KeyWithHashCollision(1))
                   ->remove(new KeyWithHashCollision(2));

        static::assertCount(1, $map);
    }

    /**
     * @covers ::containsValue()
     */
    public function testContainsValue(): void
    {
        $keys   = [1, 2, 3, 4];
        $values = ['a', 'b', 'c', 'd'];

        $map   = HashMap::fromKeyValue($keys, $values);
        $index = rand(0, count($values) - 1);

        static::assertTrue($map->containsValue($values[$index]));
        static::assertFalse($map->containsValue(-42));
    }

    /**
     * @covers ::values()
     */
    public function testValues(): void
    {
        $keys   = [1, 2, 3, 4];
        $values = ['a', 'b', 'c', 'd'];

        $map  = HashMap::fromKeyValue($keys, $values);
        $vals = $map->values();

        static::assertEquals($values, $vals);
    }

    /**
     * @covers ::keys()
     */
    public function testKeys(): void
    {
        $keys   = [1, 2, 3, 4];
        $values = ['a', 'b', 'c', 'd'];

        $map  = HashMap::fromKeyValue($keys, $values);
        $keyz = $map->keys();

        static::assertEquals($keys, $keyz);
    }

    /**
     * @covers ::pairs()
     */
    public function testPairs(): void
    {
        $keys   = [1, 2, 3, 4];
        $values = ['a', 'b', 'c', 'd'];

        $map = HashMap::fromKeyValue($keys, $values);

        $pairs = $map->pairs();
        for ($i = 0; $i < count($keys); $i++) {
            static::assertEquals($keys[$i], $pairs[$i]->getKey());
            static::assertEquals($values[$i], $pairs[$i]->getValue());
        }
    }

    /**
     * @covers ::clear()
     * @covers ::isEmpty()
     * @covers ::count()
     */
    public function testClear(): void
    {
        $keys   = [1, 2, 3, 4];
        $values = ['a', 'b', 'c', 'd'];

        $map = HashMap::fromKeyValue($keys, $values);
        $map = $map->clear();

        static::assertEquals(0, $map->count());
        static::assertTrue($map->isEmpty());
    }

    /**
     * @covers ::getIterator
     */
    public function testGetIterator(): void
    {
        $map = new HashMap(
            new Pair('a', 1),
            new Pair('b', 2),
            new Pair('c', 3)
        );

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
     * @covers ::__construct()
     * @covers ::fromKeyValue()
     */
    public function testFromKeyValue(): void
    {
        $keys   = [1, 2, 3];
        $values = ['a', 'b', 'c'];

        $map1 = HashMap::fromKeyValue($keys, $values);

        $map2 = new HashMap(
            new Pair(1, 'a'),
            new Pair(2, 'b'),
            new Pair(3, 'c')
        );

        static::assertTrue(Equal::areEquals($map1, $map2));
    }

    /**
     * @covers ::equals()
     */
    public function testEquality(): void
    {
        $keys   = [1, 2, 3];
        $values = ['a', 'b', 'c'];

        $map1 = HashMap::fromKeyValue($keys, $values);
        $map2 = HashMap::fromKeyValue($keys, $values);

        $keys   = [3, 4, 5];
        $values = ['d', 'e', 'f'];

        $map3 = HashMap::fromKeyValue($keys, $values);

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
        $keys   = [1, 2, 3];
        $values = ['a', 'b', 'c'];

        $map1 = HashMap::fromKeyValue($keys, $values);
        $map2 = HashMap::fromKeyValue($keys, $values);

        $keys   = [3, 4, 5];
        $values = ['d', 'e', 'f'];

        $map3 = HashMap::fromKeyValue($keys, $values);

        static::assertSame($map1->hash(), $map1->hash());
        static::assertSame($map1->hash(), $map2->hash());
        static::assertNotSame($map1->hash(), $map3->hash());
    }
}
