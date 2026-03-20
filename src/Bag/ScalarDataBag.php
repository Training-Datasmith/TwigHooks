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
namespace Sylius\Twig_Hooks\Bag;

class Scalar_Data_Bag extends Data_Bag implements Scalar_Data_Bag_Interface
{
    /**
     * @param array<string, scalar> $container
     */
    public function __construct(array $container = [])
    {
        $this->validate_values($container);
        parent::__construct($container);
    }
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (!is_scalar($value)) {
            throw new \InvalidArgumentException('The value must be a scalar.');
        }
        parent::offsetSet($offset, $value);
    }
    /**
     * @param array<string, mixed> $values
     */
    private function validate_values(array $values): void
    {
        foreach ($values as $value) {
            if (is_array($value)) {
                $this->validate_values($value);
                continue;
            }
            if (!is_scalar($value)) {
                throw new \InvalidArgumentException('The value must be a scalar.');
            }
        }
    }
}