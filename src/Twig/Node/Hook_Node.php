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
namespace Sylius\Twig_Hooks\Twig\Node;

use Sylius\Twig_Hooks\Twig\Runtime\Hooks_Runtime;
use Twig\Attribute\Yield_Ready;
use Twig\Compiler;
use Twig\Node\Expression\Array_Expression;
use Twig\Node\Node;
#[Yield_Ready]
final class Hook_Node extends Node
{
    public function __construct(Node $name, ?Node $context, bool $only, int $lineno)
    {
        if (\func_num_args() > 4) {
            trigger_deprecation('sylius/twig-hooks', '0.11.0', \sprintf('The "tag" constructor argument of the "%s" class is deprecated and ignored (check which TokenParser class set it to "%s"), the tag is now automatically set by the Parser when needed.', self::class, func_get_arg(4) ?: 'null'));
        }
        // Remove when twig < 3.12 support is dropped
        if (!class_exists(\Twig\Node\Expression\Function_Node\Enum_Cases_Function::class)) {
            $tag = func_get_arg(4);
            parent::__construct(['name' => $name, 'hook_level_context' => $context ?? new Array_Expression([], $lineno)], ['only' => $only], $lineno, $tag);
            return;
        }
        parent::__construct(['name' => $name, 'hook_level_context' => $context ?? new Array_Expression([], $lineno)], ['only' => $only], $lineno);
    }
    public function compile(Compiler $compiler): void
    {
        $compiler->add_debug_info($this);
        $compiler->raw(sprintf('$hooksRuntime = $this->env->getRuntime(\'%s\');', Hooks_Runtime::class))->raw("\n");
        $compiler->raw(sprintf('%s $hooksRuntime->renderHook(', class_exists(Yield_Ready::class) ? 'yield' : 'echo'));
        $compiler->subcompile($this->get_node('name'));
        $compiler->raw(', ');
        $compiler->subcompile($this->get_node('hook_level_context'));
        $compiler->raw(', ');
        $compiler->raw('$context');
        $compiler->raw(', ');
        $compiler->raw($this->get_attribute('only') ? 'true' : 'false');
        $compiler->raw(");\n");
    }
}