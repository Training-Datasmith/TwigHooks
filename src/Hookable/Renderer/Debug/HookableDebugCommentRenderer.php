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
namespace Sylius\Twig_Hooks\Hookable\Renderer\Debug;

use Sylius\Twig_Hooks\Debug\Debug_Aware_Renderer_Interface;
use Sylius\Twig_Hooks\Hookable\Abstract_Hookable;
use Sylius\Twig_Hooks\Hookable\Hookable_Component;
use Sylius\Twig_Hooks\Hookable\Hookable_Template;
use Sylius\Twig_Hooks\Hookable\Metadata\Hookable_Metadata;
use Sylius\Twig_Hooks\Hookable\Renderer\Hookable_Renderer_Interface;
final class Hookable_Debug_Comment_Renderer implements Hookable_Renderer_Interface, Debug_Aware_Renderer_Interface
{
    public function __construct(private readonly Hookable_Renderer_Interface $inner_renderer)
    {
    }
    public function render(Abstract_Hookable $hookable, Hookable_Metadata $metadata): string
    {
        $rendered_parts = [];
        $rendered_parts[] = $this->get_debug_comment($hookable, $metadata, '%s BEGIN HOOKABLE | hook: "%s", name: "%s", %s: "%s", priority: %d %s');
        $rendered_parts[] = trim($this->inner_renderer->render($hookable, $metadata));
        $rendered_parts[] = $this->get_debug_comment($hookable, $metadata, '%s  END HOOKABLE  | hook: "%s", name: "%s", %s: "%s", priority: %d %s');
        return implode(\PHP_EOL, $rendered_parts);
    }
    private function get_debug_comment(Abstract_Hookable $hookable, Hookable_Metadata $metadata, string $format): string
    {
        [$target_name, $target_value] = match ($hookable::class) {
            Hookable_Template::class => ['template', $hookable->template],
            Hookable_Component::class => ['component', $hookable->component],
            default => throw new \InvalidArgumentException('Unsupported hookable type.'),
        };
        $comment_prefix = $metadata->context[self::CONTEXT_DEBUG_PREFIX] ?? self::DEFAULT_DEBUG_PREFIX;
        $comment_suffix = $metadata->context[self::CONTEXT_DEBUG_SUFFIX] ?? self::DEFAULT_DEBUG_SUFFIX;
        return sprintf($format, $comment_prefix, $hookable->hook_name, $hookable->name, $target_name, $target_value, $hookable->priority(), $comment_suffix);
    }
}