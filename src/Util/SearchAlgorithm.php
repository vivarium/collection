<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Collection\Util;

/**
 * @template T
 * @template V
 * @psalm-immutable
 */
interface SearchAlgorithm
{
    /**
     * @param array<int, T> $array
     * @param V             $element
     */
    public function search(array $array, $element): int;

    /**
     * @param array<int, T> $array
     * @param V             $element
     */
    public function contains(array $array, $element): bool;
}
