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
class Profile
{
    /** @var array<HookProfile> */
    private array $root_profiles = [];
    private ?Hook_Profile $previous_hook_profile = null;
    private ?Hook_Profile $current_hook_profile = null;
    private ?Hookable_Profile $current_hookable_profile = null;
    private int $number_of_hooks = 0;
    private int $number_of_hookables = 0;
    /**
     * @param array<string> $hooksNames
     */
    public function register_hook_start(array $hooks_names): void
    {
        $this->previous_hook_profile = $this->current_hook_profile;
        $hook_profile = new Hook_Profile($hooks_names, [], $this->previous_hook_profile);
        $this->current_hook_profile = $hook_profile;
        $this->current_hookable_profile?->add_child($hook_profile);
    }
    public function register_hook_end(int|float|null $duration = null): void
    {
        if (null !== $this->current_hook_profile && null === $this->current_hook_profile->get_parent()) {
            $this->root_profiles[] = $this->current_hook_profile;
        }
        if (null !== $duration) {
            $this->current_hook_profile?->set_duration($duration);
        }
        $this->previous_hook_profile = $this->previous_hook_profile?->get_parent();
        $this->current_hook_profile = $this->current_hook_profile?->get_parent();
        ++$this->number_of_hooks;
    }
    public function register_hookable_render_start(Abstract_Hookable $hookable): void
    {
        if (null === $this->current_hook_profile) {
            throw new \RuntimeException('Cannot register hookable render without hook profile');
        }
        $hookable_profile = new Hookable_Profile($this->current_hook_profile, $hookable->name, $hookable, []);
        $this->current_hookable_profile = $hookable_profile;
        $this->current_hook_profile->add_hookable_profile($this->current_hookable_profile);
        ++$this->number_of_hookables;
    }
    public function register_hookable_render_end(int|float|null $duration): void
    {
        if (null !== $duration) {
            $this->current_hookable_profile?->set_duration($duration);
        }
        $this->current_hookable_profile = null;
    }
    /**
     * @return array<HookProfile>
     */
    public function get_root_profiles(): array
    {
        return $this->root_profiles;
    }
    public function get_number_of_hooks(): int
    {
        return $this->number_of_hooks;
    }
    public function get_number_of_hookables(): int
    {
        return $this->number_of_hookables;
    }
    public function get_total_duration(): int|float
    {
        $total_duration = 0;
        foreach ($this->root_profiles as $root_profile) {
            $total_duration += $root_profile->get_duration();
        }
        return $total_duration;
    }
    public function reset(): void
    {
        $this->root_profiles = [];
        $this->previous_hook_profile = null;
        $this->current_hook_profile = null;
        $this->current_hookable_profile = null;
        $this->number_of_hooks = 0;
        $this->number_of_hookables = 0;
    }
}