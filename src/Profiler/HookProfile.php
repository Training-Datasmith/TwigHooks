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

/** @internal */
class Hook_Profile
{
    private int|float|null $duration = null;
    /**
     * @param array<string> $hooksNames
     * @param array<HookableProfile> $hookablesProfiles
     */
    public function __construct(private readonly array $hooks_names, private array $hookables_profiles, private readonly ?self $parent = null)
    {
    }
    public function get_parent(): ?self
    {
        return $this->parent;
    }
    public function get_name(): string
    {
        return implode(', ', $this->hooks_names);
    }
    public function add_hookable_profile(Hookable_Profile $hookable_profile): void
    {
        $this->hookables_profiles[] = $hookable_profile;
    }
    /**
     * @return array<HookableProfile>
     */
    public function get_hookables_profiles(): array
    {
        return $this->hookables_profiles;
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