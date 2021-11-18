<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Collection\Set;

use Vivarium\Collection\Collection;

/**
 * @template T
 * @template-extends Collection<T>
 * @psalm-immutable
 */
interface Set extends Collection
{
    /**
     * @return self<T>
     */
    public function clear(): self;

    /**
     * @param T $element
     *
     * @return self<T>
     */
    public function add($element): self;

    /**
     * @param T $element
     *
     * @return self<T>
     */
    public function remove($element): self;

    /**
     * @param Set<T> $set
     *
     * @return self<T>
     */
    public function union(Set $set): self;

    /**
     * @param Set<T> $set
     *
     * @return self<T>
     */
    public function intersection(Set $set): self;

    /**
     * @param Set<T> $set
     *
     * @return self<T>
     */
    public function difference(Set $set): self;

    /**
     * @param Set<T> $set
     */
    public function isSubsetOf(Set $set): bool;
}
