<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Collection\Util;

use Vivarium\Equality\Equal;

use function count;

/**
 * @template T
 * @template V
 * @template-implements SearchAlgorithm<T, V>
 * @psalm-immutable
 */
final class LinearSearch implements SearchAlgorithm
{
    /** @var callable(T, V):bool */
    private $equals;

    /**
     * @param callable(T, V):bool|null $equals
     */
    public function __construct(?callable $equals = null)
    {
        if ($equals === null) {
            $equals =
                /**
                 * @param T $current
                 * @param V $element
                 */
                static function ($current, $element): bool {
                    return Equal::areEquals($current, $element);
                };
        }

        $this->equals = $equals;
    }

    /**
     * @param array<int, T> $array
     * @param V             $element
     */
    public function search($array, $element): int
    {
        foreach ($array as $index => $current) {
            if (($this->equals)($current, $element)) {
                return $index;
            }
        }

        return -(count($array) + 1);
    }

    /**
     * @param array<int, T> $array
     * @param V             $element
     */
    public function contains(array $array, $element): bool
    {
        return $this->search($array, $element) >= 0;
    }
}
