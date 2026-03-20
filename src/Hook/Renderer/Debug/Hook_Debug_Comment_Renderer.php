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
namespace Sylius\Twig_Hooks\Hook\Renderer\Debug;

use Sylius\Twig_Hooks\Debug\Debug_Aware_Renderer_Interface;
use Sylius\Twig_Hooks\Hook\Renderer\Hook_Renderer_Interface;
final class Hook_Debug_Comment_Renderer implements Hook_Renderer_Interface, Debug_Aware_Renderer_Interface
{
    public function __construct(private readonly Hook_Renderer_Interface $inner_renderer)
    {
    }
    public function render(array $hook_names, array $hook_context = []): string
    {
        $rendered_parts = [];
        $rendered_parts[] = $this->get_debug_comment($hook_names, $hook_context, '%s BEGIN HOOK | name: "%s" %s');
        $rendered_parts[] = trim($this->inner_renderer->render($hook_names, $hook_context));
        $rendered_parts[] = $this->get_debug_comment($hook_names, $hook_context, '%s  END HOOK  | name: "%s" %s');
        return implode(\PHP_EOL, $rendered_parts);
    }
    /**
     * @param string|array<string> $hooksNames
     * @param array<string, mixed> $hookContext
     */
    private function get_debug_comment(string|array $hooks_names, array $hook_context, string $format): string
    {
        $comment_prefix = $hook_context[self::CONTEXT_DEBUG_PREFIX] ?? self::DEFAULT_DEBUG_PREFIX;
        $comment_suffix = $hook_context[self::CONTEXT_DEBUG_SUFFIX] ?? self::DEFAULT_DEBUG_SUFFIX;
        return sprintf($format, $comment_prefix, is_string($hooks_names) ? $hooks_names : implode(', ', $hooks_names), $comment_suffix);
    }
}