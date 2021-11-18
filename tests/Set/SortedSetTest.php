<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Collection\Test\Set;

use PHPUnit\Framework\TestCase;
use stdClass;
use Vivarium\Collection\Set\SortedSet;
use Vivarium\Comparator\Comparator;
use Vivarium\Comparator\IntegerComparator;
use Vivarium\Comparator\StringComparator;
use Vivarium\Equality\Equal;
use Vivarium\Equality\Equality;

/**
 * @coversDefaultClass \Vivarium\Collection\Set\SortedSet
 */
class SortedSetTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::add()
     * @covers ::count()
     * @covers ::toArray()
     */
    public function testAdd(): void
    {
        /** @var int[] $values */
        $values = [5, 2, 4, 3, 1];

        $set = new SortedSet(new IntegerComparator(), ...$values);
        $set = $set
            ->add(3)
            ->add(6);

        $expected = [1, 2, 3, 4, 5, 6];

        static::assertCount(6, $set);
        static::assertEquals($expected, $set->toArray());
        static::assertNotSame($set, $set->add(42));
    }

    /**
     * @covers ::__construct()
     * @covers ::remove()
     * @covers ::count()
     * @covers ::toArray()
     */
    public function testRemove(): void
    {
        /** @var int[] $values */
        $values = [5, 2, 3, 1, 7];

        $set = new SortedSet(new IntegerComparator(), ...$values);
        $set = $set->remove(3);

        $expected = [1, 2, 5, 7];

        static::assertCount(4, $set);
        static::assertEquals($expected, $set->toArray());
        static::assertNotSame($set, $set->remove(7));
    }

    /**
     * @covers ::clear()
     * @covers ::isEmpty()
     */
    public function testClear(): void
    {
        /** @var int[] $values */
        $values = [3, 2, 1];

        $set = new SortedSet(new IntegerComparator(), ...$values);
        $set = $set->clear();

        static::assertCount(0, $set);
        static::assertTrue($set->isEmpty());
    }

    /**
     * @covers ::getIterator()
     */
    public function testGetIterator(): void
    {
        /** @var int[] $values */
        $values   = [3, 2, 1, 3, 1, 3];
        $expected = [1, 2, 3];

        $set   = SortedSet::fromArray(new IntegerComparator(), $values);
        $index = 0;
        foreach ($set as $item) {
            static::assertEquals($expected[$index], $item);
            ++$index;
        }
    }

    /**
     * @covers ::contains()
     */
    public function testContains(): void
    {
        /** @var string[] $values */
        $values = ['c', 'd', 'e', 'a'];

        $set = new SortedSet(new StringComparator(), ...$values);

        static::assertTrue($set->contains('d'));
        static::assertFalse($set->contains('z'));
    }

    /**
     * @covers ::contains()
     */
    public function testContainsWithObjects(): void
    {
        $stub1 = $this->createMock(Equality::class);
        $stub2 = $this->createMock(Equality::class);
        $stub3 = $this->createMock(Equality::class);

        $stub2->method('equals')
              ->with($stub1)
              ->willReturn(false);

        $stub2->method('equals')
              ->with($stub2)
              ->willReturn(true);

        $stub2->method('equals')
              ->with($stub3)
              ->willReturn(false);

        $comparator = $this->createMock(Comparator::class);

        $comparator->method('compare')->with($stub1, $stub1)->willReturn(0);
        $comparator->method('compare')->with($stub1, $stub2)->willReturn(-1);
        $comparator->method('compare')->with($stub1, $stub3)->willReturn(-1);
        $comparator->method('compare')->with($stub2, $stub1)->willReturn(1);
        $comparator->method('compare')->with($stub2, $stub2)->willReturn(0);
        $comparator->method('compare')->with($stub2, $stub3)->willReturn(-1);
        $comparator->method('compare')->with($stub3, $stub1)->willReturn(1);
        $comparator->method('compare')->with($stub3, $stub2)->willReturn(1);
        $comparator->method('compare')->with($stub3, $stub3)->willReturn(0);

        $set = new SortedSet($comparator, $stub1, $stub2, $stub2, $stub3);

        static::assertTrue($set->contains($stub1));
    }

    /**
     * @covers ::union()
     * @covers ::add()
     * @covers ::toArray()
     */
    public function testUnion(): void
    {
        /** @var int[] $values1 */
        $values1 = [5, 3, 2, 42, 7];

        /** @var int[] $values2 */
        $values2 = [6, 27, 1, 3, 7];

        $set1 = new SortedSet(new IntegerComparator(), ...$values1);
        $set2 = new SortedSet(new IntegerComparator(), ...$values2);

        $set = $set1->union($set2);

        $expected = [1, 2, 3, 5, 6, 7, 27, 42];

        static::assertCount(8, $set);
        static::assertEquals($expected, $set->toArray());
    }

    /**
     * @covers ::intersection()
     * @covers ::count()
     * @covers ::toArray()
     */
    public function testIntersection(): void
    {
        /** @var int[] $values1 */
        $values1 = [5, 4, 2, 3, 6];

        $set1 = new SortedSet(new IntegerComparator(), ...$values1);

        /** @var int[] $values2 */
        $values2 = [8, 7, 4, 5, 6, 1];

        $set2 = new SortedSet(new IntegerComparator(), ...$values2);

        $set = $set1->intersection($set2);

        $expected = [4, 5, 6];

        static::assertCount(3, $set);
        static::assertEquals($expected, $set->toArray());
    }

    /**
     * @covers ::difference()
     * @covers ::count()
     * @covers ::toArray()
     */
    public function testDifference(): void
    {
        /** @var int[] $values1 */
        $values1 = [4, 5, 1, 2, 3];

        $set1 = new SortedSet(new IntegerComparator(), ...$values1);

        /** @var int[] $values2 */
        $values2 = [8, 7, 4, 6, 5, 2];

        $set2 = new SortedSet(new IntegerComparator(), ...$values2);

        $set = $set1->difference($set2);

        $expected = [1, 3];

        static::assertCount(2, $set);
        static::assertEquals($expected, $set->toArray());
    }

    /**
     * @covers ::isSubsetOf()
     */
    public function testIsSubsetOf(): void
    {
        /** @var int[] $values1 */
        $values1 = [4, 5, 1, 2, 6, 3];

        $set = new SortedSet(new IntegerComparator(), ...$values1);

        /** @var int[] $values2 */
        $values2 = [3, 2, 1];

        $subset = new SortedSet(new IntegerComparator(), ...$values2);

        static::assertTrue($subset->isSubsetOf($set));
        static::assertFalse($set->isSubsetOf($subset));
    }

    /**
     * @covers ::union()
     */
    public function testUnionImmutability(): void
    {
        /** @var int[] $values */
        $values = [1, 2, 3];

        $set1 = new SortedSet(new IntegerComparator(), ...$values);
        $set2 = new SortedSet(new IntegerComparator());

        $set = $set1->union($set2);

        static::assertNotSame($set, $set1);
    }

    /**
     * @covers ::fromArray()
     */
    public function testFromArray(): void
    {
        /** @var int[] $values */
        $values = [3, 3, 2, 2, 1, 1];

        $set1 = SortedSet::fromArray(new IntegerComparator(), $values);
        $set2 = new SortedSet(new IntegerComparator(), ...$values);

        static::assertTrue(Equal::areEquals($set1, $set2));
    }

    /**
     * @covers ::equals()
     */
    public function testEquals(): void
    {
        /** @var int[] $values1 */
        $values1 = [1, 2, 3, 3, 1];

        $set1 = new SortedSet(new IntegerComparator(), ...$values1);
        $set2 = new SortedSet(new IntegerComparator(), ...$values1);

        /** @var int[] $values2 */
        $values2 = [4, 4, 5, 2, 3];

        $set3 = new SortedSet(new IntegerComparator(), ...$values2);

        static::assertTrue($set1->equals($set1));
        static::assertTrue($set1->equals($set2));
        static::assertFalse($set1->equals($set3));
        static::assertFalse($set1->equals(new stdClass()));
    }

    /**
     * @covers ::hash()
     */
    public function testHash(): void
    {
        /** @var int[] $values1 */
        $values1 = [1, 2, 3, 3, 1];

        $set1 = new SortedSet(new IntegerComparator(), ...$values1);
        $set2 = new SortedSet(new IntegerComparator(), ...$values1);

        /** @var int[] $values2 */
        $values2 = [4, 4, 5, 2, 3];

        $set3 = new SortedSet(new IntegerComparator(), ...$values2);

        static::assertSame($set1->hash(), $set1->hash());
        static::assertSame($set1->hash(), $set2->hash());
        static::assertNotSame($set1->hash(), $set3->hash());
    }
}
