<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Collection\Pair;

use Vivarium\Equality\Equality;
use Vivarium\Equality\EqualsBuilder;
use Vivarium\Equality\HashBuilder;

/**
 * @template-covariant K
 * @template-covariant V
 * @psalm-immutable
 */
final class Pair implements Equality
{
    /** @var K */
    private $key;

    /** @var V */
    private $value;

    /**
     * @param K $key
     * @param V $value
     */
    public function __construct($key, $value)
    {
        $this->key   = $key;
        $this->value = $value;
    }

    /**
     * @return K
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return V
     */
    public function getValue()
    {
        return $this->value;
    }

    public function equals(object $object): bool
    {
        if (! $object instanceof Pair) {
            return false;
        }

        if ($this === $object) {
            return true;
        }

        return (new EqualsBuilder())
            ->append($this->getKey(), $object->getKey())
            ->append($this->getValue(), $object->getValue())
            ->isEquals();
    }

    public function hash(): string
    {
        return (new HashBuilder())
            ->append($this->key)
            ->append($this->value)
            ->getHashCode();
    }
}
