<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare (strict_types=1);
namespace Sylius\Twig_Hooks\Hook\Metadata;

use Sylius\Twig_Hooks\Bag\Data_Bag_Interface;
class Hook_Metadata
{
    public function __construct(public readonly string $name, public readonly Data_Bag_Interface $context)
    {
    }
}