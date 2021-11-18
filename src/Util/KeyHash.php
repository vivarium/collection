<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

namespace Vivarium\Collection\Util;

use Vivarium\Equality\Equal;

use function is_int;
use function is_string;

final class KeyHash
{
    /**
     * @param mixed $key
     *
     * @return array-key
     *
     * @psalm-mutation-free
     */
    public static function hash($key)
    {
        return is_int($key) || is_string($key) ? $key : Equal::hash($key);
    }
}
