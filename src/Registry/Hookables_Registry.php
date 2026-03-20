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
namespace Sylius\Twig_Hooks\Registry;

use Laminas\Stdlib\SplPriorityQueue;
use Sylius\Twig_Hooks\Hookable\Abstract_Hookable;
use Sylius\Twig_Hooks\Hookable\Disabled_Hookable;
use Sylius\Twig_Hooks\Hookable\Merger\Hookable_Merger_Interface;
/** @internal */
class Hookables_Registry
{
    /** @var array<string, array<AbstractHookable>> */
    private array $hookables = [];
    /**
     * @param iterable<AbstractHookable> $hookables
     */
    public function __construct(iterable $hookables, private readonly Hookable_Merger_Interface $hookable_merger)
    {
        /** @var AbstractHookable $hookable */
        foreach ($hookables as $hookable) {
            if (!$hookable instanceof Abstract_Hookable) {
                throw new \InvalidArgumentException(sprintf('All elements must be an instance of "%s".', Abstract_Hookable::class));
            }
            $this->hookables[$hookable->hook_name][$hookable->name] = $hookable;
        }
    }
    /**
     * @return array<string>
     */
    public function get_hook_names(): array
    {
        return array_keys($this->hookables);
    }
    /**
     * @param string|array<string> $hooksNames
     *
     * @return array<AbstractHookable>
     */
    public function get_enabled_for(string|array $hooks_names): array
    {
        $hooks_names = is_string($hooks_names) ? [$hooks_names] : $hooks_names;
        $hookables = array_values(array_filter($this->merge_hookables($hooks_names), static fn(Abstract_Hookable $hookable): bool => !$hookable instanceof Disabled_Hookable));
        $priority_queue = new SplPriorityQueue();
        foreach ($hookables as $hookable) {
            $priority_queue->insert($hookable, $hookable->priority());
        }
        return $priority_queue->to_array();
    }
    /**
     * @param string|array<string> $hooksNames
     *
     * @return array<AbstractHookable>
     */
    public function get_for(string|array $hooks_names): array
    {
        $hooks_names = is_string($hooks_names) ? [$hooks_names] : $hooks_names;
        $hookables = $this->merge_hookables($hooks_names);
        $priority_queue = new SplPriorityQueue();
        foreach ($hookables as $hookable) {
            $priority_queue->insert($hookable, $hookable->priority());
        }
        return $priority_queue->to_array();
    }
    /**
     * @param array<string> $hooksNames
     *
     * @return array<AbstractHookable>
     */
    private function merge_hookables(array $hooks_names): array
    {
        /** @var array<AbstractHookable> $mergedHookables */
        $merged_hookables = [];
        foreach (array_reverse($hooks_names) as $hook_name) {
            $hookables = $this->hookables[$hook_name] ?? [];
            foreach ($hookables as $hookable_name => $hookable) {
                if (array_key_exists($hookable_name, $merged_hookables)) {
                    $hookable = $this->hookable_merger->merge($merged_hookables[$hookable_name], $hookable);
                }
                $merged_hookables[$hookable_name] = $hookable;
            }
        }
        return array_values($merged_hookables);
    }
}