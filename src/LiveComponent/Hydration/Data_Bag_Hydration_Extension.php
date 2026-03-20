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
namespace Sylius\Twig_Hooks\Live_Component\Hydration;

use Sylius\Twig_Hooks\Bag\Data_Bag_Interface;
use Symfony\UX\Live_Component\Hydration\Hydration_Extension_Interface;
final class Data_Bag_Hydration_Extension implements Hydration_Extension_Interface
{
    public function supports(string $class_name): bool
    {
        return is_a($class_name, Data_Bag_Interface::class, true);
    }
    public function hydrate(mixed $value, string $class_name): object
    {
        return new $class_name($value);
    }
    public function dehydrate(object $object): mixed
    {
        if (!$object instanceof Data_Bag_Interface) {
            throw new \InvalidArgumentException(sprintf('Object must implement %s', Data_Bag_Interface::class));
        }
        return $object->all();
    }
}