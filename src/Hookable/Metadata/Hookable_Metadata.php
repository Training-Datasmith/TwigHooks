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
class Hookable_Metadata
{
    /**
     * @param array<string> $prefixes
     */
    public function __construct(public readonly Hook_Metadata $rendered_by, public readonly Data_Bag_Interface $context, public readonly Scalar_Data_Bag_Interface $configuration, public readonly array $prefixes = [])
    {
        foreach ($prefixes as $prefix) {
            if (!is_string($prefix)) {
                throw new \InvalidArgumentException('Parent name must be a string.');
            }
        }
    }
    public function has_prefixes(): bool
    {
        return count($this->prefixes) > 0;
    }
    public function with_configuration(Scalar_Data_Bag_Interface $configuration): self
    {
        return new self($this->rendered_by, $this->context, $configuration, $this->prefixes);
    }
}