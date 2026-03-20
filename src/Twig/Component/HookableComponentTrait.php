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
namespace Sylius\Twig_Hooks\Twig\Component;

use Sylius\Twig_Hooks\Hookable\Metadata\Hookable_Metadata;
use Symfony\UX\Twig_Component\Attribute\Expose_In_Template;
trait Hookable_Component_Trait
{
    #[Expose_In_Template('hookable_metadata')]
    public ?Hookable_Metadata $hookable_metadata = null;
}