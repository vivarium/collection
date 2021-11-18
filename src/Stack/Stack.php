<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Collection\Stack;

use Vivarium\Collection\Collection;

/**
 * @template T
 * @template-extends Collection<T>
 * @psalm-immutable
 */
interface Stack extends Collection
{
    /**
     * @param T $element
     *
     * @return self<T>
     */
    public function push($element): self;

    /**
     * @return self<T>
     */
    public function pop(): self;

    /**
     * @return T
     */
    public function peek();

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
     * @return self<T>
     */
    public function clear(): self;
}
