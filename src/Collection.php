<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Collection;

use Countable;
use Iterator;
use IteratorAggregate;
use Vivarium\Equality\Equality;

/**
 * @template T
 * @template-extends IteratorAggregate<int, T>
 * @psalm-immutable
 */
interface Collection extends Countable, IteratorAggregate, Equality
{
    /**
     * @param T $element
     *
     * @return Collection<T>
     */
    public function add($element): Collection;

    /**
     * @param T $element
     *
     * @return self<T>
     */
    public function remove($element): self;

    /**
     * @param T $element
     */
    public function contains($element): bool;

    public function isEmpty(): bool;

    /**
     * @return self<T>
     */
    public function clear(): self;

    /**
     * @return array<int, T>
     */
    public function toArray(): array;

    /**
     * @return Iterator<int, T>
     */
    public function getIterator(): Iterator;

    public function count(): int;
}
