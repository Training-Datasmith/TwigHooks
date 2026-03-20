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

use Sylius\Twig_Hooks\Hookable\Hookable_Component;
use Sylius\Twig_Hooks\Hookable\Metadata\Hookable_Metadata;
use Sylius\Twig_Hooks\Provider\Exception\Invalid_Expression_Exception;
interface Props_Provider_Interface
{
    /**
     * @throws InvalidExpressionException
     *
     * @return array<string, mixed>
     */
    public function provide(Hookable_Component $hookable, Hookable_Metadata $metadata): array;
}