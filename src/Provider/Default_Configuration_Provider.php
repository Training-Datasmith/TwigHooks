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
namespace Sylius\Twig_Hooks\Provider;

use Sylius\Twig_Hooks\Hookable\Abstract_Hookable;
final class Default_Configuration_Provider implements Configuration_Provider_Interface
{
    public function provide(Abstract_Hookable $hookable): array
    {
        return $hookable->configuration;
    }
}