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
namespace Sylius\Twig_Hooks\Twig\Token_Parser;

use Sylius\Twig_Hooks\Twig\Node\Hook_Node;
use Twig\Node\Node;
use Twig\Node\Nodes;
use Twig\Token;
use Twig\Token_Parser\Abstract_Token_Parser;
final class Hook_Token_Parser extends Abstract_Token_Parser
{
    public const TAG = 'hook';
    public function parse(Token $token): Node
    {
        $lineno = $token->get_line();
        $stream = $this->parser->get_stream();
        if (method_exists($this->parser, 'parseExpression')) {
            $hooks_names = $this->parser->parse_expression();
        } else {
            // Remove when Twig 3.21 support is dropped
            $hooks_names = $this->parser->get_expression_parser()->parse_expression();
        }
        $hook_context = null;
        if ($stream->next_if(Token::NAME_TYPE, 'with')) {
            if (method_exists($this->parser, 'parseExpression')) {
                $hook_context = $this->parse_multitarget_expression();
            } else {
                // Remove when Twig 3.21 support is dropped
                $hook_context = $this->parser->get_expression_parser()->parse_multitarget_expression();
            }
        }
        $only = false;
        if ($stream->next_if(Token::NAME_TYPE, 'only')) {
            $only = true;
        }
        $stream->expect(Token::BLOCK_END_TYPE);
        if (class_exists(\Twig\Node\Expression\Function_Node\Enum_Cases_Function::class)) {
            return new Hook_Node($hooks_names, $hook_context, $only, $lineno);
        }
        // Remove when twig < 3.12 support is dropped
        return new Hook_Node($hooks_names, $hook_context, $only, $lineno, $this->get_tag());
    }
    public function get_tag(): string
    {
        return self::TAG;
    }
    private function parse_multitarget_expression(): Nodes
    {
        $targets = [];
        while (true) {
            $targets[] = $this->parser->parse_expression();
            if (!$this->parser->get_stream()->next_if(Token::PUNCTUATION_TYPE, ',')) {
                break;
            }
        }
        return new Nodes($targets);
    }
}