<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Collection\Map;

use Countable;
use Iterator;
use IteratorAggregate;
use Vivarium\Collection\Pair\Pair;
use Vivarium\Equality\Equality;

/**
 * @template K
 * @template V
 * @template-extends IteratorAggregate<K, V>
 * @psalm-immutable
 */
interface Map extends Countable, IteratorAggregate, Equality
{
    /**
     * @param K $key
     * @param V $value
     *
     * @return self<K, V>
     */
    public function put($key, $value): self;

    /**
     * @param K $key
     *
     * @return V
     */
    public function get($key);

    /**
     * @param K $key
     *
     * @return self<K, V>
     */
    public function remove($key): self;

    /**
     * @param K $key
     */
    public function containsKey($key): bool;

    /**
     * @param mixed $value
     */
    public function containsValue($value): bool;

    /**
     * @return array<int, V>
     */
    public function values(): array;

    /**
     * @return array<int, K>
     */
    public function keys(): array;

    /**
     * @return array<int, Pair<K, V>>
     */
    public function pairs(): array;

    public function isEmpty(): bool;

    /**
     * @return self<K, V>
     */
    public function clear(): self;

    /**
     * @return Iterator<K, V>
     */
    public function getIterator(): Iterator;

    public function count(): int;
}
