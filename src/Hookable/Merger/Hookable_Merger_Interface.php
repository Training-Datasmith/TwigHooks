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
namespace Sylius\Twig_Hooks\Hookable\Merger;

use Sylius\Twig_Hooks\Hookable\Abstract_Hookable;
interface Hookable_Merger_Interface
{
    public function merge(Abstract_Hookable ...$hookables): Abstract_Hookable;
}