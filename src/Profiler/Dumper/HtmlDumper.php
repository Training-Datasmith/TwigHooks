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
namespace Sylius\Twig_Hooks\Profiler\Dumper;

use Sylius\Twig_Hooks\Hookable\Abstract_Hookable;
use Sylius\Twig_Hooks\Hookable\Hookable_Component;
use Sylius\Twig_Hooks\Hookable\Hookable_Template;
use Sylius\Twig_Hooks\Profiler\Hookable_Profile;
use Sylius\Twig_Hooks\Profiler\Hook_Profile;
use Sylius\Twig_Hooks\Profiler\Profile;
/** @internal */
final class Html_Dumper
{
    public function dump(Profile $profile): string
    {
        return sprintf('<pre>%s%s</pre>', \PHP_EOL, $this->dump_profile($profile));
    }
    private function dump_profile(Profile $profile): string
    {
        $root_profiles = $profile->get_root_profiles();
        $str = '';
        foreach ($root_profiles as $root_profile) {
            $str .= $this->dump_hook_profile($root_profile);
        }
        return $str;
    }
    private function dump_hook_profile(Hook_Profile $hook_profile, string $prefix = '', bool $sibling = false): string
    {
        $str = sprintf('%s└ <span><span class="status-info">(Hook)</span> %s</span>', $prefix, $hook_profile->get_name());
        $str .= \PHP_EOL;
        $prefix .= $sibling ? '│   ' : '    ';
        $number_of_hookables = \count($hook_profile->get_hookables_profiles());
        foreach ($hook_profile->get_hookables_profiles() as $index => $hookable_profile) {
            $str .= $this->dump_hookable_profile($hookable_profile, $prefix, $index + 1 !== $number_of_hookables);
        }
        return $str;
    }
    private function dump_hookable_profile(Hookable_Profile $hookable_profile, string $prefix = '', bool $sibling = false): string
    {
        $target_name = match ($hookable_profile->get_hookable()::class) {
            Hookable_Template::class => 'Template',
            Hookable_Component::class => 'Component',
            default => throw new \InvalidArgumentException(sprintf('Unsupported hookable type %s', $hookable_profile->get_hookable()::class)),
        };
        $str = sprintf('%s└ <span><span class="%s">(%s)</span> [↑ %d, ⏲ %d ms] %s (%s)</span>', $prefix, $target_name === 'Template' ? 'status-success' : 'status-warning', $target_name, $hookable_profile->get_hookable()->priority(), $hookable_profile->get_duration(), $hookable_profile->get_name(), $this->get_target_value($hookable_profile->get_hookable()));
        $str .= \PHP_EOL;
        $prefix .= $sibling ? '│   ' : '    ';
        $number_of_children = \count($hookable_profile->get_children());
        foreach ($hookable_profile->get_children() as $index => $child) {
            $str .= $this->dump_hook_profile($child, $prefix, $index + 1 !== $number_of_children);
        }
        return $str;
    }
    private function get_target_value(Abstract_Hookable $hookable): string
    {
        return match ($hookable::class) {
            Hookable_Template::class => $hookable->template,
            Hookable_Component::class => $hookable->component,
            default => throw new \InvalidArgumentException(sprintf('Unsupported hookable type %s', $hookable::class)),
        };
    }
}