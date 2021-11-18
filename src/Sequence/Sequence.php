<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Collection\Sequence;

use Vivarium\Collection\Collection;
use Vivarium\Comparator\Comparator;

/**
 * @template T
 * @template-extends Collection<T>
 * @psalm-immutable
 */
interface Sequence extends Collection
{
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
     * @param T $element
     *
     * @return self<T>
     */
    public function setAtIndex(int $index, $element): self;

    /**
     * @return self<T>
     */
    public function removeAtIndex(int $index): self;

    /**
     * @return self<T>
     */
    public function clear(): self;

    /**
     * @return T
     */
    public function getAtIndex(int $index);

    /**
     * @param T $element
     */
    public function search($element): int;

    /**
     * @param Comparator<T> $comparator
     *
     * @return self<T>
     */
    public function sort(Comparator $comparator): self;
}
