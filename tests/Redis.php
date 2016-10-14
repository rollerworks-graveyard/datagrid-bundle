<?php

declare(strict_types=1);

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

// Poly fill for Redis, symfony defines this as a service
// but the service validator chokes on the existence of this class.
if (!class_exists('Redis')) {
    final class Redis
    {
    }
}
