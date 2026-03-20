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
namespace Sylius\Twig_Hooks\Twig\Runtime;

use Sylius\Twig_Hooks\Bag\Data_Bag_Interface;
use Sylius\Twig_Hooks\Bag\Scalar_Data_Bag_Interface;
use Sylius\Twig_Hooks\Hook\Normalizer\Name\Name_Normalizer_Interface;
use Sylius\Twig_Hooks\Hook\Normalizer\Prefix\Prefix_Normalizer_Interface;
use Sylius\Twig_Hooks\Hook\Renderer\Hook_Renderer_Interface;
use Sylius\Twig_Hooks\Hookable\Metadata\Hookable_Metadata;
use Twig\Error\Runtime_Error;
use Twig\Extension\Runtime_Extension_Interface;
use Webmozart\Assert\Assert;
final class Hooks_Runtime implements Runtime_Extension_Interface
{
    public const HOOKABLE_METADATA = 'hookable_metadata';
    public function __construct(private readonly Hook_Renderer_Interface $hook_renderer, private readonly Name_Normalizer_Interface $name_normalizer, private readonly Prefix_Normalizer_Interface $prefix_normalizer, private readonly bool $enable_autoprefixing)
    {
    }
    /**
     * @param array<string, mixed> $context
     *
     * @throws RuntimeError
     */
    public function get_hookable_metadata(array $context): Hookable_Metadata
    {
        $hookable_metadata = $context[self::HOOKABLE_METADATA] ?? null;
        if (!$hookable_metadata instanceof Hookable_Metadata) {
            throw new Runtime_Error('Trying to access hookable context inside a non-hookable.');
        }
        return $hookable_metadata;
    }
    /**
     * @param array<string, mixed> $context
     *
     * @throws RuntimeError
     */
    public function get_hookable_context(array $context): Data_Bag_Interface
    {
        return $this->get_hookable_metadata($context)->context;
    }
    /**
     * @param array<string, mixed> $context
     *
     * @throws RuntimeError
     */
    public function get_hookable_configuration(array $context): Scalar_Data_Bag_Interface
    {
        return $this->get_hookable_metadata($context)->configuration;
    }
    /**
     * @param array<string, mixed> $context
     */
    public function is_hookable(array $context): bool
    {
        return array_key_exists(self::HOOKABLE_METADATA, $context) && $context[self::HOOKABLE_METADATA] instanceof Hookable_Metadata;
    }
    /**
     * @param string|array<string> $hookNames
     * @param array<string, mixed> $twigVars
     * @param array<string, mixed> $hookContext
     */
    public function render_hook(string|array $hook_names, array $hook_context = [], array $twig_vars = [], bool $only = false): string
    {
        $hook_names = is_string($hook_names) ? [$hook_names] : $hook_names;
        $hook_names = array_map($this->name_normalizer->normalize(...), $hook_names);
        $hookable_metadata = $twig_vars[self::HOOKABLE_METADATA] ?? null;
        Assert::null_or_is_instance_of($hookable_metadata, Hookable_Metadata::class);
        unset($twig_vars[self::HOOKABLE_METADATA]);
        $context = $this->get_context($hook_context, $twig_vars, $hookable_metadata, $only);
        $prefixes = $this->get_prefixes($hook_context, $hookable_metadata);
        if (false === $this->enable_autoprefixing || [] === $prefixes) {
            return $this->hook_renderer->render($hook_names, $context);
        }
        $prefixed_hook_names = [];
        foreach ($hook_names as $hook_name) {
            foreach ($prefixes as $prefix) {
                $format = str_starts_with($hook_name, '#') ? '%s%s' : '%s.%s';
                $prefixed_hook_names[] = sprintf($format, $prefix, $hook_name);
            }
        }
        return $this->hook_renderer->render($prefixed_hook_names, $context);
    }
    /**
     * @param array<string, mixed> $hookContext
     *
     * @return array<string>
     */
    private function get_prefixes(array $hook_context, ?Hookable_Metadata $hookable_metadata): array
    {
        $prefixes = [];
        if ($hookable_metadata !== null && $hookable_metadata->has_prefixes()) {
            $prefixes = $hookable_metadata->prefixes;
        }
        if (array_key_exists('_prefixes', $hook_context)) {
            $prefixes = $hook_context['_prefixes'];
        }
        return array_map($this->prefix_normalizer->normalize(...), $prefixes);
    }
    /**
     * @param array<string, mixed> $hookContext
     * @param array<string, mixed> $twigVars
     *
     * @return array<string, mixed>
     */
    private function get_context(array $hook_context, array $twig_vars, ?Hookable_Metadata $hookable_metadata, bool $only = false): array
    {
        if ($only) {
            return $hook_context;
        }
        $context = $hookable_metadata?->context->all() ?? [];
        return array_merge($twig_vars, $context, $hook_context);
    }
}