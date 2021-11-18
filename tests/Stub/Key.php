<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

namespace Vivarium\Collection\Test\Stub;

use Vivarium\Equality\Equality;
use Vivarium\Equality\EqualsBuilder;
use Vivarium\Equality\HashBuilder;

/**
 * @psalm-immutable
 */
final class Key implements Equality
{
    private int $n;

    public function __construct(int $n)
    {
        $this->n = $n;
    }

    public function equals(object $object): bool
    {
        if (! $object instanceof Key) {
            return false;
        }

        if ($object === $this) {
            return true;
        }

        return (new EqualsBuilder())
            ->append($this->n, $object->n)
            ->isEquals();
    }

    public function hash(): string
    {
        return (new HashBuilder())
            ->append($this->n)
            ->getHashCode();
    }
}
