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
namespace Sylius\Twig_Hooks\Hookable\Metadata;

use Sylius\Twig_Hooks\Bag\Data_Bag_Interface;
use Sylius\Twig_Hooks\Bag\Scalar_Data_Bag_Interface;
use Sylius\Twig_Hooks\Hook\Metadata\Hook_Metadata;
use Sylius\Twig_Hooks\Hook\Normalizer\Prefix\Prefix_Normalizer_Interface;
final class Hookable_Metadata_Factory implements Hookable_Metadata_Factory_Interface
{
    public function __construct(private readonly Prefix_Normalizer_Interface $prefix_normalizer)
    {
    }
    public function create(Hook_Metadata $hook_metadata, Data_Bag_Interface $context, Scalar_Data_Bag_Interface $configuration, array $prefixes = []): Hookable_Metadata
    {
        $prefixes = array_map($this->prefix_normalizer->normalize(...), $prefixes);
        return new Hookable_Metadata($hook_metadata, $context, $configuration, $prefixes);
    }
}