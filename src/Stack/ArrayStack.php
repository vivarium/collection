<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Collection\Stack;

use ArrayIterator;
use DomainException;
use Iterator;
use Vivarium\Collection\Sequence\ArraySequence;
use Vivarium\Equality\EqualsBuilder;
use Vivarium\Equality\HashBuilder;

use function abs;
use function array_reverse;
use function count;

/**
 * @template T
 * @template-implements Stack<T>
 * @psalm-immutable
 */
final class ArrayStack implements Stack
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
     * @return ArrayStack<K>
     *
     * @template K
     */
    public static function fromArray(array $elements): ArrayStack
    {
        return new ArrayStack(...$elements);
    }

    /**
     * @param T $element
     *
     * @return ArrayStack<T>
     */
    public function push($element): ArrayStack
    {
        $stack           = clone $this;
        $stack->elements = $stack->elements->add($element);

        return $stack;
    }

    /**
     * @return ArrayStack<T>
     */
    public function pop(): ArrayStack
    {
        if ($this->isEmpty()) {
            throw new DomainException('Cannot pop from an empty stack.');
        }

        $stack           = clone $this;
        $stack->elements = $stack->elements->removeAtIndex($this->count() - 1);

        return $stack;
    }

    /**
     * @param T $element
     *
     * @return ArrayStack<T>
     */
    public function add($element): ArrayStack
    {
        return $this->push($element);
    }

    /**
     * @param T $element
     *
     * @return ArrayStack<T>
     */
    public function remove($element): ArrayStack
    {
        $stack = clone $this;
        $index = $this->count() - abs($this->elements->search($element));
        for ($i = 0; $i < $index; $i++) {
            $stack = $stack->pop();
        }

        return $stack;
    }

    /**
     * @param T $element
     */
    public function contains($element): bool
    {
        return $this->elements->contains($element);
    }

    /**
     * @return T
     */
    public function peek()
    {
        if ($this->isEmpty()) {
            throw new DomainException('Cannot peek from an empty stack.');
        }

        return $this->elements->getAtIndex($this->count() - 1);
    }

    /**
     * @return ArrayStack<T>
     */
    public function clear(): ArrayStack
    {
        return new ArrayStack();
    }

    /**
     * @return array<int, T>
     */
    public function toArray(): array
    {
        return array_reverse($this->elements->toArray());
    }

    /**
     * @return Iterator<int, T>
     */
    public function getIterator(): Iterator
    {
        return new ArrayIterator(
            array_reverse(
                $this->elements->toArray()
            )
        );
    }

    public function count(): int
    {
        return count($this->elements);
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    public function equals(object $object): bool
    {
        if (! $object instanceof ArrayStack) {
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
