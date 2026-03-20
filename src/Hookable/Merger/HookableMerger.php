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
namespace Sylius\Twig_Hooks\Hookable\Merger;

use Sylius\Twig_Hooks\Hookable\Abstract_Hookable;
final class Hookable_Merger implements Hookable_Merger_Interface
{
    /**
     * @throws \ReflectionException
     */
    public function merge(Abstract_Hookable ...$hookables): Abstract_Hookable
    {
        if ([] === $hookables) {
            throw new \InvalidArgumentException('At least one hookable must be passed to merge.');
        }
        /** @var class-string<AbstractHookable> $class */
        $class = end($hookables)::class;
        $serialized_hookables = array_map(static fn(Abstract_Hookable $hookable): array => $hookable->to_array(), $hookables);
        $inputs = array_merge(...$serialized_hookables);
        $arguments = $this->create_constructor_arguments($class, $inputs);
        return new $class(...$arguments);
    }
    /**
     * @param class-string $class
     * @param array<string, mixed> $inputs
     *
     * @return array<string>
     *
     * @throws \ReflectionException
     */
    private function create_constructor_arguments(string $class, array $inputs): array
    {
        $reflection = new \ReflectionClass($class);
        /** @var \ReflectionMethod $constructor */
        $constructor = $reflection->get_constructor();
        $parameters = array_map(static fn(\ReflectionParameter $parameter): string => $parameter->get_name(), $constructor->get_parameters());
        $arguments = [];
        foreach ($inputs as $input_name => $input) {
            if (!in_array($input_name, $parameters, true)) {
                continue;
            }
            $arguments[$input_name] = $input;
        }
        return $arguments;
    }
}