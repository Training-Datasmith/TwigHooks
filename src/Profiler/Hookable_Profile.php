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
namespace Sylius\Twig_Hooks\Profiler;

use Sylius\Twig_Hooks\Hookable\Abstract_Hookable;
/** @internal */
class Hookable_Profile
{
    private int|float|null $duration = null;
    /**
     * @param array<HookProfile> $children
     */
    public function __construct(private readonly Hook_Profile $parent, private readonly string $name, private readonly Abstract_Hookable $hookable, private array $children)
    {
    }
    public function get_parent(): Hook_Profile
    {
        return $this->parent;
    }
    public function get_name(): string
    {
        return $this->name;
    }
    public function get_hookable(): Abstract_Hookable
    {
        return $this->hookable;
    }
    public function add_child(Hook_Profile $child): void
    {
        $this->children[] = $child;
    }
    /**
     * @return array<HookProfile>
     */
    public function get_children(): array
    {
        return $this->children;
    }
    public function set_duration(int|float $duration): void
    {
        $this->duration = $duration;
    }
    public function get_duration(): int|float|null
    {
        return $this->duration;
    }
}