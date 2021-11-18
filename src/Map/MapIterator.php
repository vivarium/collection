<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Collection\Map;

use Iterator;
use Vivarium\Collection\Pair\Pair;

use function array_values;
use function count;
use function is_array;

/**
 * @template K
 * @template V
 * @template-implements Iterator<K, V>
 */
final class MapIterator implements Iterator
{
    /** @var array<int, Pair<K, V>|array<int, Pair<K, V>>> */
    private array $pairs;

    private int $current;

    private int $index;

    private int $count;

    /**
     * @param array<array-key, Pair<K, V>|array<int, Pair<K, V>>> $pairs
     */
    public function __construct(array $pairs)
    {
        $this->pairs   = array_values($pairs);
        $this->current = 0;
        $this->index   = 0;

        $this->count = count($this->pairs);
    }

    /**
     * @return V
     */
    public function current()
    {
        $bucket = $this->pairs[$this->current];
        if (is_array($bucket)) {
            return $bucket[$this->index]->getValue();
        }

        return $bucket->getValue();
    }

    public function next(): void
    {
        $bucket = $this->pairs[$this->current];
        if (is_array($bucket) && isset($bucket[$this->index + 1])) {
            $this->index += 1;

            return;
        }

        $this->index    = 0;
        $this->current += 1;
    }

    /**
     * @return K
     */
    public function key()
    {
        $bucket = $this->pairs[$this->current];
        if (is_array($bucket)) {
            return $bucket[$this->index]->getKey();
        }

        return $bucket->getKey();
    }

    public function valid(): bool
    {
        if ($this->current >= $this->count) {
            return false;
        }

        $bucket = $this->pairs[$this->current];

        return ! is_array($bucket) || isset($bucket[$this->index]);
    }

    public function rewind(): void
    {
        $this->current = 0;
        $this->index   = 0;
    }
}
