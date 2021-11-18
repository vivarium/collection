<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Collection\Queue;

use Iterator;
use Vivarium\Comparator\Comparator;
use Vivarium\Equality\EqualsBuilder;
use Vivarium\Equality\HashBuilder;

use function usort;

/**
 * @template T
 * @template-implements Queue<T>
 * @psalm-immutable
 */
final class PriorityQueue implements Queue
{
    /** @var ArrayQueue<T> */
    private ArrayQueue $queue;

    /** @var Comparator<T> */
    private Comparator $comparator;

    /**
     * @param Comparator<T> $comparator
     * @param T             ...$elements
     */
    public function __construct(Comparator $comparator, ...$elements)
    {
        $this->comparator = $comparator;

        usort($elements, $comparator);

        $this->queue = new ArrayQueue(...$elements);
    }

    /**
     * @param Comparator<K>       $comparator
     * @param array<array-key, K> $elements
     *
     * @return PriorityQueue<K>
     *
     * @template K
     */
    public static function fromArray(Comparator $comparator, array $elements): PriorityQueue
    {
        return new PriorityQueue($comparator, ...$elements);
    }

    /**
     * @param T $element
     *
     * @return PriorityQueue<T>
     */
    public function add($element): PriorityQueue
    {
        return $this->enqueue($element);
    }

    /**
     * @param T $element
     *
     * @return PriorityQueue<T>
     */
    public function remove($element): PriorityQueue
    {
        $priority        = clone $this;
        $priority->queue = $priority->queue->remove($element);

        return $priority;
    }

    /**
     * @return Iterator<int, T>
     */
    public function getIterator(): Iterator
    {
        return $this->queue->getIterator();
    }

    public function count(): int
    {
        return $this->queue->count();
    }

    /**
     * @return PriorityQueue<T>
     */
    public function clear(): PriorityQueue
    {
        return new PriorityQueue($this->comparator);
    }

    /**
     * @param T $element
     *
     * @return PriorityQueue<T>
     */
    public function enqueue($element): PriorityQueue
    {
        $priority   = clone $this;
        $elements   = $priority->queue->toArray();
        $elements[] = $element;

        usort($elements, $this->comparator);
        $priority->queue = new ArrayQueue(...$elements);

        return $priority;
    }

    /**
     * @return PriorityQueue<T>
     */
    public function dequeue(): Queue
    {
        $priorityQueue        = clone $this;
        $priorityQueue->queue = $priorityQueue->queue->dequeue();

        return $priorityQueue;
    }

    /**
     * @return T
     */
    public function peek()
    {
        return $this->queue->peek();
    }

    /**
     * @return array<int, T>
     */
    public function toArray(): array
    {
        return $this->queue->toArray();
    }

    /**
     * @param T $element
     */
    public function contains($element): bool
    {
        return $this->queue->contains($element);
    }

    public function isEmpty(): bool
    {
        return $this->queue->isEmpty();
    }

    public function equals(object $object): bool
    {
        if (! $object instanceof PriorityQueue) {
            return false;
        }

        if ($object === $this) {
            return true;
        }

        return (new EqualsBuilder())
            ->append($this->queue, $object->queue)
            ->isEquals();
    }

    public function hash(): string
    {
        return (new HashBuilder())
            ->append($this->queue)
            ->getHashCode();
    }
}
