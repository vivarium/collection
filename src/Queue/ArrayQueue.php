<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Collection\Queue;

use DomainException;
use Iterator;
use Vivarium\Collection\Sequence\ArraySequence;
use Vivarium\Equality\EqualsBuilder;
use Vivarium\Equality\HashBuilder;

/**
 * @template T
 * @template-implements Queue<T>
 * @psalm-immutable
 */
final class ArrayQueue implements Queue
{
    /** @var ArraySequence<T> */
    private ArraySequence $elements;

    /**
     * @param T ...$elements
     *
     * @no-named-arguments
     */
    public function __construct(...$elements)
    {
        $this->elements = ArraySequence::fromArray($elements);
    }

    /**
     * @param array<int, K> $elements
     *
     * @return ArrayQueue<K>
     *
     * @template K
     */
    public static function fromArray(array $elements): ArrayQueue
    {
        return new ArrayQueue(...$elements);
    }

    /**
     * @param T $element
     *
     * @return ArrayQueue<T>
     */
    public function add($element): ArrayQueue
    {
        return $this->enqueue($element);
    }

    /**
     * @param T $element
     *
     * @return ArrayQueue<T>
     */
    public function remove($element): ArrayQueue
    {
        $queue  = clone $this;
        $remove = $queue->elements->search($element);
        if ($remove >= 0) {
            for ($i = 0; $i < $remove + 1; $i++) {
                $queue = $queue->dequeue();
            }
        }

        return $queue;
    }

    /**
     * @return ArrayQueue<T>
     */
    public function clear(): ArrayQueue
    {
        return new ArrayQueue();
    }

    /**
     * @return array<int, T>
     */
    public function toArray(): array
    {
        return $this->elements->toArray();
    }

    /**
     * @return Iterator<int, T>
     */
    public function getIterator(): Iterator
    {
        return $this->elements->getIterator();
    }

    public function count(): int
    {
        return $this->elements->count();
    }

    /**
     * @param T $element
     *
     * @return ArrayQueue<T>
     */
    public function enqueue($element): Queue
    {
        $queue           = clone $this;
        $queue->elements = $queue->elements->add($element);

        return $queue;
    }

    /**
     * @return ArrayQueue<T>
     */
    public function dequeue(): Queue
    {
        if ($this->isEmpty()) {
            throw new DomainException('Cannot dequeue from an empty queue.');
        }

        $queue           = clone $this;
        $queue->elements = $queue->elements->removeAtIndex(0);

        return $queue;
    }

    /**
     * @return T
     */
    public function peek()
    {
        if ($this->isEmpty()) {
            throw new DomainException('Cannot peek from an empty queue.');
        }

        return $this->elements->getAtIndex(0);
    }

    /**
     * @param T $element
     */
    public function contains($element): bool
    {
        return $this->elements->contains($element);
    }

    public function isEmpty(): bool
    {
        return $this->elements->isEmpty();
    }

    public function equals(object $object): bool
    {
        if (! $object instanceof ArrayQueue) {
            return false;
        }

        if ($object === $this) {
            return true;
        }

        return (new EqualsBuilder())
            ->append($this->elements, $object->elements)
            ->isEquals();
    }

    public function hash(): string
    {
        return (new HashBuilder())
            ->append($this->elements)
            ->getHashCode();
    }
}
