<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Collection\Test\Stack;

use DomainException;
use PHPUnit\Framework\TestCase;
use stdClass;
use Vivarium\Collection\Stack\ArrayStack;
use Vivarium\Equality\Equal;

/**
 * @coversDefaultClass \Vivarium\Collection\Stack\ArrayStack
 */
class ArrayStackTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::push()
     * @covers ::count()
     * @covers ::peek()
     * @covers ::toArray()
     */
    public function testPush(): void
    {
        /** @var ArrayStack<int> $stack */
        $stack = new ArrayStack(1, 2, 3);
        $stack = $stack->push(4);

        static::assertCount(4, $stack);
        static::assertEquals(4, $stack->peek());
        static::assertEquals([4, 3, 2, 1], $stack->toArray());
        static::assertNotSame($stack, $stack->push(42));
    }

    /**
     * @covers ::add()
     */
    public function testAdd(): void
    {
        /** @var ArrayStack<int> $stack */
        $stack = new ArrayStack(1, 2, 3);
        $stack = $stack->add(4);

        static::assertCount(4, $stack);
        static::assertEquals(4, $stack->peek());
        static::assertEquals([4, 3, 2, 1], $stack->toArray());
        static::assertNotSame($stack, $stack->push(42));
    }

    /**
     * @covers ::pop()
     * @covers ::isEmpty()
     * @covers ::peek()
     */
    public function testPop(): void
    {
        /** @var array<int, string> $elements */
        $elements = ['a', 'b', 'c', 'd'];
        $stack    = ArrayStack::fromArray($elements);

        $index = 3;
        while (! $stack->isEmpty()) {
            $element = $stack->peek();
            $stack   = $stack->pop();

            static::assertEquals($elements[$index], $element);
            --$index;
        }

        static::assertTrue($stack->isEmpty());

        $stack = $stack->push('e');

        static::assertNotSame($stack, $stack->pop());
    }

    /**
     * @covers ::remove()
     * @covers ::toArray()
     */
    public function testRemove(): void
    {
        $elements = [1, 2, 3, 4, 5, 6];
        $expected = [1];

        $stack = ArrayStack::fromArray($elements);
        $stack = $stack->remove(4);
        $stack = $stack->remove(2);

        static::assertEquals($expected, $stack->toArray());

        $stack = $stack->pop();
        static::assertNotSame($stack, $stack->remove(1));
    }

    /**
     * @covers ::pop()
     */
    public function testPopOnEmpty(): void
    {
        static::expectException(DomainException::class);
        static::expectExceptionMessage('Cannot pop from an empty stack.');

        $stack = new ArrayStack();

        /** @psalm-suppress UnusedMethodCall */
        $stack->pop();
    }

    /**
     * @covers ::peek()
     */
    public function testPeekOnEmpty(): void
    {
        static::expectException(DomainException::class);
        static::expectExceptionMessage('Cannot peek from an empty stack.');

        $stack = new ArrayStack();
        /** @psalm-suppress UnusedMethodCall */
        $stack->peek();
    }

    /**
     * @covers ::contains()
     */
    public function testContains(): void
    {
        /** @var ArrayStack<int> $stack */
        $stack = new ArrayStack(1, 2, 3, 4, 5);

        static::assertTrue($stack->contains(3));
        static::assertFalse($stack->contains(42));
    }

    /**
     * @covers ::clear()
     */
    public function testClear(): void
    {
        $stack = new ArrayStack(1, 2, 3);
        $stack = $stack->clear();

        static::assertCount(0, $stack);
    }

    /**
     * @covers ::getIterator()
     */
    public function testGetIterator(): void
    {
        $stack = new ArrayStack(1, 2, 3);
        $order = [3, 2, 1];

        $index    = 0;
        $iterator = $stack->getIterator();
        while ($iterator->valid()) {
            static::assertEquals($order[$index], $iterator->current());
            $iterator->next();
            ++$index;
        }
    }

    /**
     * @covers ::fromArray()
     */
    public function testFromArray(): void
    {
        $stack1 = ArrayStack::fromArray([1, 2, 3, 4, 5]);
        $stack2 = new ArrayStack(1, 2, 3, 4, 5);

        static::assertTrue(Equal::areEquals($stack1, $stack2));
    }

    /**
     * @covers ::equals()
     */
    public function testEquals(): void
    {
        $stack1 = new ArrayStack(1, 2, 3);
        $stack2 = new ArrayStack(1, 2, 3);
        $stack3 = new ArrayStack(4, 5, 6);

        static::assertTrue($stack1->equals($stack1));
        static::assertTrue($stack1->equals($stack2));
        static::assertFalse($stack1->equals($stack3));
        static::assertFalse($stack1->equals(new stdClass()));
    }

    /**
     * @covers ::hash()
     */
    public function testHash(): void
    {
        $stack1 = new ArrayStack(1, 2, 3);
        $stack2 = new ArrayStack(1, 2, 3);
        $stack3 = new ArrayStack(4, 5, 6);

        static::assertSame($stack1->hash(), $stack1->hash());
        static::assertSame($stack1->hash(), $stack2->hash());
        static::assertNotSame($stack1->hash(), $stack3->hash());
    }
}
