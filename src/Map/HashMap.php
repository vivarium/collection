<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Collection\Map;

use OutOfBoundsException;
use Vivarium\Collection\Pair\Pair;
use Vivarium\Collection\Util\KeyHash;
use Vivarium\Collection\Util\LinearSearch;
use Vivarium\Collection\Util\SearchAlgorithm;
use Vivarium\Collection\Util\Vector;
use Vivarium\Equality\Equal;
use Vivarium\Equality\EqualsBuilder;
use Vivarium\Equality\HashBuilder;

use function array_map;
use function count;

/**
 * @template K
 * @template V
 * @template-implements Map<K, V>
 * @psalm-immutable
 */
final class HashMap implements Map
{
    /** @var array<array-key, array<int, Pair<K, V>>> */
    private array $pairs;

    /**
     * @param Pair<K, V> ...$pairs
     */
    public function __construct(Pair ...$pairs)
    {
        $this->pairs = [];
        foreach ($pairs as $pair) {
            $hash = KeyHash::hash($pair->getKey());
            if (! isset($this->pairs[$hash])) {
                $this->pairs[$hash] = [];
            }

            $this->pairs[$hash] = Vector::putInPlace(
                $this->pairs[$hash],
                $pair,
                $this->searchByPair()
            );
        }
    }

    /**
     * @param K0[] $keys
     * @param V0[] $values
     *
     * @return HashMap<K0, V0>
     *
     * @template K0
     * @template V0
     */
    public static function fromKeyValue(array $keys, array $values): HashMap
    {
        $pairs = array_map(
            /**
             * @param K0 $key
             * @param V0 $value
             *
             * @return Pair<K0, V0>
             */
            static function ($key, $value): Pair {
                return new Pair($key, $value);
            },
            $keys,
            $values
        );

        return new HashMap(...$pairs);
    }

    /**
     * @param K $key
     * @param V $value
     *
     * @return HashMap<K, V>
     */
    public function put($key, $value): HashMap
    {
        $map = clone $this;

        $hash = KeyHash::hash($key);
        if (! isset($map->pairs[$hash])) {
            $map->pairs[$hash] = [];
        }

        $map->pairs[$hash] = Vector::putInPlace(
            $map->pairs[$hash],
            new Pair($key, $value),
            $this->searchByPair()
        );

        return $map;
    }

    /**
     * @param K $key
     *
     * @return V
     */
    public function get($key)
    {
        $hash = KeyHash::hash($key);

        if (isset($this->pairs[$hash])) {
            $index = $this->searchByKey()->search($this->pairs[$hash], $key);
            if ($index >= 0) {
                return $this->pairs[$hash][$index]->getValue();
            }
        }

        throw new OutOfBoundsException('The provided key is not present.');
    }

    /**
     * @param K $key
     *
     * @return HashMap<K, V>
     */
    public function remove($key): HashMap
    {
        if (! $this->containsKey($key)) {
            return $this;
        }

        $map  = clone $this;
        $hash = KeyHash::hash($key);

        $index = $map->searchByKey()->search($map->pairs[$hash], $key);
        if ($index >= 0) {
            unset($map->pairs[$hash][$index]);
        }

        if (count($map->pairs[$hash]) === 0) {
            unset($map->pairs[$hash]);
        }

        return $map;
    }

    /**
     * @return array<int, Pair<K, V>>
     */
    public function pairs(): array
    {
        $pairs = [];
        foreach ($this->pairs as $bucket) {
            foreach ($bucket as $pair) {
                $pairs[] = $pair;
            }
        }

        return $pairs;
    }

    /**
     * @param mixed $value
     */
    public function containsValue($value): bool
    {
        foreach ($this->pairs as $bucket) {
            foreach ($bucket as $pair) {
                if (Equal::areEquals($value, $pair->getValue())) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return array<int, V>
     */
    public function values(): array
    {
        $values = [];
        foreach ($this->pairs as $bucket) {
            foreach ($bucket as $pair) {
                $values[] = $pair->getValue();
            }
        }

        return $values;
    }

    /**
     * @return array<int, K>
     */
    public function keys(): array
    {
        $keys = [];
        foreach ($this->pairs as $bucket) {
            foreach ($bucket as $pair) {
                $keys[] = $pair->getKey();
            }
        }

        return $keys;
    }

    /**
     * @param K $key
     */
    public function containsKey($key): bool
    {
        $hash = KeyHash::hash($key);

        return isset($this->pairs[$hash]) && $this->searchByKey()->contains($this->pairs[$hash], $key);
    }

    /**
     * @return HashMap<K, V>
     */
    public function clear(): HashMap
    {
        return new HashMap();
    }

    public function count(): int
    {
        $count = 0;
        foreach ($this->pairs as $bucket) {
            $count += count($bucket);
        }

        return $count;
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    /**
     * @return MapIterator<K, V>
     */
    public function getIterator(): MapIterator
    {
        return new MapIterator($this->pairs);
    }

    public function equals(object $object): bool
    {
        if (! $object instanceof HashMap) {
            return false;
        }

        if ($object === $this) {
            return true;
        }

        return (new EqualsBuilder())
            ->append($this->pairs, $object->pairs)
            ->isEquals();
    }

    public function hash(): string
    {
        return (new HashBuilder())
            ->append($this->pairs)
            ->getHashCode();
    }

    /**
     * @return SearchAlgorithm<Pair<K, V>, Pair<K, V>>
     */
    private function searchByPair(): SearchAlgorithm
    {
        return new LinearSearch(
            /**
             * @param Pair<K, V> $pair1
             * @param Pair<K, V> $pair2
             */
            static function (Pair $pair1, Pair $pair2): bool {
                return Equal::areEquals($pair1->getKey(), $pair2->getKey());
            }
        );
    }

    /**
     * @return SearchAlgorithm<Pair<K, V>, K>
     */
    private function searchByKey(): SearchAlgorithm
    {
        return new LinearSearch(
            /**
             * @param Pair<K, V> $pair
             * @param K          $key
             */
            static function (Pair $pair, $key): bool {
                return Equal::areEquals($pair->getKey(), $key);
            }
        );
    }
}
