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
namespace Sylius\Twig_Hooks\Hookable\Renderer;

use Sylius\Twig_Hooks\Hookable\Abstract_Hookable;
interface Supportable_Hookable_Renderer_Interface extends Hookable_Renderer_Interface
{
    public function supports(Abstract_Hookable $hookable): bool;
}