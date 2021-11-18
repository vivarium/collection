<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Collection\Test\Queue;

use PHPUnit\Framework\TestCase;
use stdClass;
use Vivarium\Collection\Queue\PriorityQueue;
use Vivarium\Comparator\IntegerComparator;
use Vivarium\Comparator\StringComparator;
use Vivarium\Equality\Equal;

use function usort;

/**
 * @coversDefaultClass \Vivarium\Collection\Queue\PriorityQueue
 */
class PriorityQueueTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::enqueue()
     * @covers ::peek()
     * @covers ::count()
     */
    public function testEnqueue(): void
    {
        $queue = new PriorityQueue(new IntegerComparator());
        $queue = $queue
            ->enqueue(4)
            ->enqueue(3)
            ->enqueue(3)
            ->enqueue(1);

        static::assertCount(4, $queue);
        static::assertEquals(1, $queue->peek());
        static::assertNotSame($queue, $queue->enqueue(7));
    }

    /**
     * @covers ::add()
     */
    public function testAdd(): void
    {
        /** @var int[] $values */
        $values = [1, 2, 3];

        $queue = new PriorityQueue(new IntegerComparator(), ...$values);
        $queue = $queue->add(4);

        static::assertCount(4, $queue);
        static::assertEquals(1, $queue->peek());
        static::assertEquals([1, 2, 3, 4], $queue->toArray());
        static::assertNotSame($queue, $queue->enqueue(5));
    }

    /**
     * @covers ::dequeue()
     * @covers ::isEmpty()
     * @covers ::peek()
     */
    public function testDequeue(): void
    {
        /** @var string[] $elements */
        $elements = ['z', 'k', 'v', 'a', 'd'];
        $queue    = PriorityQueue::fromArray(new StringComparator(), $elements);

        usort($elements, new StringComparator());

        $index = 0;
        while (! $queue->isEmpty()) {
            $element = $queue->peek();
            $queue   = $queue->dequeue();

            static::assertEquals($elements[$index], $element);
            ++$index;
        }

        static::assertTrue($queue->isEmpty());

        $queue = $queue->enqueue('e');
        static::assertNotSame($queue, $queue->dequeue());
    }

    /**
     * @covers ::contains()
     */
    public function testContains(): void
    {
        /** @var int[] $values */
        $values = [1, 2, 3, 4, 5];

        $queue = new PriorityQueue(new IntegerComparator(), ...$values);

        static::assertTrue($queue->contains(3));
        static::assertFalse($queue->contains(42));
    }

    /**
     * @covers ::remove()
     * @covers ::toArray()
     */
    public function testRemove(): void
    {
        /** @var int[] $elements */
        $elements = [3, 4, 5, 1, 2, 6];
        $expected = [2, 3, 4, 5, 6];

        $queue = PriorityQueue::fromArray(new IntegerComparator(), $elements);
        $queue = $queue->remove(1);

        static::assertEquals($expected, $queue->toArray());
        static::assertNotSame($queue, $queue->remove(0));
    }

    /**
     * @covers ::clear()
     * @covers ::count()
     */
    public function testClear(): void
    {
        /** @var int[] $values */
        $values = [5, 2, 1];

        $queue = new PriorityQueue(new IntegerComparator(), ...$values);
        $queue = $queue->clear();

        static::assertCount(0, $queue);
    }

    /**
     * @covers ::getIterator()
     */
    public function testGetIterator(): void
    {
        /** @var int[] $values */
        $values = [3, 2, 1];

        $queue = new PriorityQueue(new IntegerComparator(), ...$values);
        $order = [1, 2, 3];

        $index    = 0;
        $iterator = $queue->getIterator();
        while ($iterator->valid()) {
            static::assertEquals($order[$index], $iterator->current());
            $iterator->next();
            ++$index;
        }
    }

    /**
     * @covers ::toArray()
     */
    public function testToArray(): void
    {
        /** @var int[] $values */
        $values = [42, 6, 3, 23, 1];

        $queue = new PriorityQueue(new IntegerComparator(), ...$values);

        $expected = [1, 3, 6, 23, 42];

        static::assertEquals($expected, $queue->toArray());
    }

    /**
     * @covers ::fromArray()
     */
    public function testFromArray(): void
    {
        /** @var int[] $values */
        $values = [1, 2, 3];

        $queue1 = PriorityQueue::fromArray(new IntegerComparator(), $values);
        $queue2 = new PriorityQueue(new IntegerComparator(), ...$values);

        static::assertTrue(Equal::areEquals($queue1, $queue2));
    }

    /**
     * @covers ::equals()
     */
    public function testEquals(): void
    {
        /** @var int[] $values1 */
        $values1 = [1, 2, 3];

        $queue1 = new PriorityQueue(new IntegerComparator(), ...$values1);
        $queue2 = new PriorityQueue(new IntegerComparator(), ...$values1);

        /** @var int[] $values2 */
        $values2 = [4, 5, 6];

        $queue3 = new PriorityQueue(new IntegerComparator(), ...$values2);

        static::assertTrue($queue1->equals($queue1));
        static::assertTrue($queue1->equals($queue2));
        static::assertFalse($queue1->equals($queue3));
        static::assertFalse($queue1->equals(new stdClass()));
    }

    /**
     * @covers ::hash()
     */
    public function testHash(): void
    {
        /** @var int[] $values1 */
        $values1 = [1, 2, 3];

        $queue1 = new PriorityQueue(new IntegerComparator(), ...$values1);
        $queue2 = new PriorityQueue(new IntegerComparator(), ...$values1);

        /** @var int[] $values2 */
        $values2 = [4, 5, 6];

        $queue3 = new PriorityQueue(new IntegerComparator(), ...$values2);

        static::assertSame($queue1->hash(), $queue1->hash());
        static::assertSame($queue1->hash(), $queue2->hash());
        static::assertNotSame($queue1->hash(), $queue3->hash());
    }
}
