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

class Disabled_Hookable extends Abstract_Hookable
{
    public function to_array(): array
    {
        return ['hookName' => $this->hook_name, 'name' => $this->name, 'context' => $this->context, 'configuration' => $this->configuration, 'priority' => $this->priority()];
    }
}