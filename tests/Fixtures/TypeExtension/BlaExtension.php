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

namespace Rollerworks\Bundle\DatagridBundle\Tests\Fixtures\TypeExtension;

use Rollerworks\Component\Datagrid\Column\AbstractTypeExtension;
use Rollerworks\Component\Datagrid\Extension\Core\Type\ColumnType;

final class BlaExtension extends AbstractTypeExtension
{
    public function getExtendedType()
    {
        return ColumnType::class;
    }
}
