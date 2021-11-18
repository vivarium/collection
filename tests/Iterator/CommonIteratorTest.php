<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

namespace Vivarium\Collection\Test\Iterator;

use Iterator;
use PHPUnit\Framework\TestCase;
use Vivarium\Equality\Equal;

use function array_values;

abstract class CommonIteratorTest extends TestCase
{
    /**
     * @param Iterator<T, int> $iterator
     * @param int[]            $keys
     * @param int[]            $values
     * @param callable(int): T $map
     *
     * @template T
     */
    protected function doTest(Iterator $iterator, array $keys, array $values, callable $map): void
    {
        $this->iterate($iterator, $keys, $values, $map);

        $iterator->rewind();

        $this->iterate($iterator, $keys, $values, $map);
    }

    /**
     * @param Iterator<T, int> $iterator
     * @param int[]            $expectedKeys
     * @param int[]            $expectedValues
     * @param callable(int): T $map
     *
     * @template T
     */
    private function iterate(Iterator $iterator, array $expectedKeys, array $expectedValues, callable $map): void
    {
        $keys   = [];
        $values = [];
        while ($iterator->valid()) {
            $keys[]   = $iterator->key();
            $values[] = $iterator->current();
            $iterator->next();
        }

        $this->checkKeys($expectedKeys, $keys, $map);
        $this->checkValues($expectedValues, $values);
    }

    /**
     * @param int[]            $expected
     * @param T[]              $keys
     * @param callable(int): T $map
     *
     * @template T
     */
    private function checkKeys(array $expected, array $keys, callable $map): void
    {
        static::assertSameSize($expected, $keys);
        foreach ($expected as $value) {
            static::assertTrue($this->contains($map($value), $keys));
        }
    }

    /**
     * @param int[] $expected
     * @param int[] $values
     */
    private function checkValues(array $expected, array $values): void
    {
        static::assertSameSize($expected, $values);
        foreach (array_values($expected) as $value) {
            static::assertTrue($this->contains($value, $values));
        }
    }

    /**
     * @param T                   $needle
     * @param array<array-key, T> $array
     *
     * @template T
     */
    private function contains($needle, array $array): bool
    {
        foreach ($array as $value) {
            if (Equal::areEquals($needle, $value)) {
                return true;
            }
        }

        return false;
    }
}
