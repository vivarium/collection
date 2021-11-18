<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

namespace Vivarium\Collection\MultiMap;

use Countable;
use Iterator;
use IteratorAggregate;
use Vivarium\Collection\Collection;
use Vivarium\Collection\Pair\Pair;
use Vivarium\Equality\Equality;

/**
 * @template K
 * @template V
 * @template-extends IteratorAggregate<K, V>
 * @psalm-immutable
 */
interface MultiMap extends Countable, IteratorAggregate, Equality
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
     * @return Collection<V>
     */
    public function get($key): Collection;

    /**
     * @param K $key
     * @param V $value
     *
     * @return self<K, V>
     */
    public function remove($key, $value): self;

    /**
     * @param K $key
     *
     * @return self<K, V>
     */
    public function removeAll($key): self;

    /**
     * @param K $key
     */
    public function containsKey($key): bool;

    /**
     * @param K $key
     * @param V $value
     */
    public function containsKeyValue($key, $value): bool;

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
