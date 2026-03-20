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
namespace Sylius\Twig_Hooks\Hookable;

class Hookable_Template extends Abstract_Hookable
{
    public function __construct(string $hook_name, string $name, public readonly string $template, array $context = [], array $configuration = [], ?int $priority = null)
    {
        parent::__construct($hook_name, $name, $context, $configuration, $priority);
    }
    public function to_array(): array
    {
        return ['hookName' => $this->hook_name, 'name' => $this->name, 'template' => $this->template, 'context' => $this->context, 'configuration' => $this->configuration, 'priority' => $this->priority()];
    }
}