<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Collection\Test\Sequence;

use OutOfBoundsException;
use PHPUnit\Framework\TestCase;
use Vivarium\Collection\Sequence\ArraySequence;

use function count;
use function sprintf;

/**
 * @coversDefaultClass \Vivarium\Collection\Sequence\ArraySequence
 */
class ArraySequenceExceptionTest extends TestCase
{
    /**
     * @param array<int, int> $elements
     *
     * @covers ::getAtIndex()
     * @covers ::checkBounds()
     * @dataProvider getTestIndexOutOfBoundData
     */
    public function testGetAtIndexOutOfBound(array $elements, int $index): void
    {
        $message = sprintf('Index out of bound. Count: %s, Index: %s', count($elements), $index);
        static::expectException(OutOfBoundsException::class);
        static::expectExceptionMessage($message);

        $list = ArraySequence::fromArray($elements);
        /** @psalm-suppress UnusedMethodCall */
        $list->getAtIndex($index);
    }

    /**
     * @param array<int, int> $elements
     *
     * @covers ::setAtIndex()
     * @covers ::checkBounds()
     * @dataProvider getTestIndexOutOfBoundData
     */
    public function testSetAtIndexOutOfBound(array $elements, int $index): void
    {
        $message = sprintf('Index out of bound. Count: %s, Index: %s', count($elements), $index);
        static::expectException(OutOfBoundsException::class);
        static::expectExceptionMessage($message);

        $list = ArraySequence::fromArray($elements);
        /** @psalm-suppress UnusedMethodCall */
        $list->setAtIndex($index, 42);
    }

    /**
     * @param array<int, int> $elements
     *
     * @covers ::removeAtIndex()
     * @covers ::checkBounds()
     * @dataProvider getTestIndexOutOfBoundData
     */
    public function testRemoveAtIndexOutOfBound(array $elements, int $index): void
    {
        $message = sprintf('Index out of bound. Count: %s, Index: %s', count($elements), $index);
        static::expectException(OutOfBoundsException::class);
        static::expectExceptionMessage($message);

        $list = ArraySequence::fromArray($elements);
        /** @psalm-suppress UnusedMethodCall */
        $list->removeAtIndex($index);
    }

    /**
     * @return array<string, array{0: array<int, int>, 1: int}>
     */
    public function getTestIndexOutOfBoundData(): array
    {
        return [
            'Empty List lower bound' =>
                [
                    [],
                    -1,
                ],
            'Empty List upper bound' =>
                [
                    [],
                    5,
                ],
            'Full list lower bound' =>
                [
                    [1, 2, 3, 4, 5],
                    -2,
                ],
            'Full list upper bound' =>
                [
                    [1, 2, 3, 4, 5],
                    5,
                ],
        ];
    }
}
