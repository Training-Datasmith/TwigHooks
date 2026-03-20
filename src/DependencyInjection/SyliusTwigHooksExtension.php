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
namespace Sylius\Twig_Hooks\Dependency_Injection;

use Sylius\Twig_Hooks\Hookable\Disabled_Hookable;
use Sylius\Twig_Hooks\Hookable\Hookable_Component;
use Sylius\Twig_Hooks\Hookable\Hookable_Template;
use Symfony\Component\Config\File_Locator;
use Symfony\Component\Dependency_Injection\Container_Builder;
use Symfony\Component\Dependency_Injection\Extension\Extension;
use Symfony\Component\Dependency_Injection\Loader\Php_File_Loader;
final class Sylius_Twig_Hooks_Extension extends Extension
{
    public function load(array $configs, Container_Builder $container): void
    {
        $loader = new Php_File_Loader($container, new File_Locator(dirname(__DIR__, 2) . '/config'));
        $loader->load('services.php');
        if ($container->has_parameter('kernel.debug') && $container->get_parameter('kernel.debug')) {
            $loader->load('services/debug/twig_events.php');
        }
        $configuration = $this->get_configuration([], $container);
        $config = $this->process_configuration($configuration, $configs);
        $this->register_hooks($container, $config['hooks'], $config['supported_hookable_types']);
        $container->set_parameter('sylius_twig_hooks.enable_autoprefixing', $config['enable_autoprefixing']);
        $container->set_parameter('sylius_twig_hooks.hook_name_section_separator', $config['hook_name_section_separator']);
    }
    /**
     * @param array<string, mixed> $hooks
     * @param array<string, string> $supportedHookableTypes
     */
    private function register_hooks(Container_Builder $container, array $hooks, array $supported_hookable_types): void
    {
        foreach ($hooks as $hook_name => $hookables) {
            foreach ($hookables as $hookable_name => $hookable) {
                if (!array_key_exists($hookable['type'], $supported_hookable_types)) {
                    throw new \InvalidArgumentException(sprintf('Hookable type "%s" is not supported.', $hookable['type']));
                }
                $this->register_hookable($container, $supported_hookable_types[$hookable['type']], $hook_name, $hookable_name, $hookable);
            }
        }
    }
    /**
     * @param array<string, mixed> $hookable
     */
    private function register_hookable(Container_Builder $container, string $class, string $hook_name, string $hookable_name, array $hookable): void
    {
        match ($class) {
            Hookable_Template::class => $this->register_template_hookable($container, $hook_name, $hookable_name, $hookable),
            Hookable_Component::class => $this->register_component_hookable($container, $hook_name, $hookable_name, $hookable),
            Disabled_Hookable::class => $this->register_disabled_hookable($container, $hook_name, $hookable_name),
            default => throw new \InvalidArgumentException(sprintf('Unsupported hookable class "%s".', $class)),
        };
    }
    /**
     * @param array<string, mixed> $hookable
     */
    private function register_template_hookable(Container_Builder $container, string $hook_name, string $hookable_name, array $hookable): void
    {
        $container->register(sprintf('sylius_twig_hooks.hook.%s.hookable.%s', $hook_name, $hookable_name), Hookable_Template::class)->set_arguments([$hook_name, $hookable_name, $hookable['template'], $hookable['context'], $hookable['configuration'], $hookable['priority'], $hookable['enabled']])->add_tag('sylius_twig_hooks.hookable', ['priority' => $hookable['priority']]);
    }
    /**
     * @param array<string, mixed> $hookable
     */
    private function register_component_hookable(Container_Builder $container, string $hook_name, string $hookable_name, array $hookable): void
    {
        $container->register(sprintf('sylius_twig_hooks.hook.%s.hookable.%s', $hook_name, $hookable_name), Hookable_Component::class)->set_arguments([$hook_name, $hookable_name, $hookable['component'], $hookable['props'] ?? [], $hookable['context'], $hookable['configuration'], $hookable['priority'], $hookable['enabled']])->add_tag('sylius_twig_hooks.hookable', ['priority' => $hookable['priority']]);
    }
    private function register_disabled_hookable(Container_Builder $container, string $hook_name, string $hookable_name): void
    {
        $container->register(sprintf('sylius_twig_hooks.hook.%s.hookable.%s', $hook_name, $hookable_name), Disabled_Hookable::class)->set_arguments([$hook_name, $hookable_name, [], [], null])->add_tag('sylius_twig_hooks.hookable', ['priority' => 0]);
    }
}