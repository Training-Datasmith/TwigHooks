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

abstract class Abstract_Hookable
{
    public readonly string $id;
    public readonly string $hook_name;
    public readonly string $name;
    public const DEFAULT_PRIORITY = 0;
    /**
     * @param array<string, mixed> $context
     * @param array<string, mixed> $configuration
     */
    public function __construct(string $hook_name, string $name, public readonly array $context = [], public readonly array $configuration = [], private readonly ?int $priority = null)
    {
        $this->id = sprintf('%s#%s', $hook_name, $name);
        $this->hook_name = $hook_name;
        $this->name = $name;
    }
    public function priority(): int
    {
        return $this->priority ?? self::DEFAULT_PRIORITY;
    }
    /**
     * @return array<string, mixed>
     */
    abstract public function to_array(): array;
}