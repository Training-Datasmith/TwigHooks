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
namespace Sylius\Twig_Hooks\Hook\Renderer;

interface Hook_Renderer_Interface
{
    /**
     * @param array<string> $hookNames
     * @param array<string, mixed> $hookContext
     */
    public function render(array $hook_names, array $hook_context = []): string;
}