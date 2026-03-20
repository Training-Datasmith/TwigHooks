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
namespace Sylius\Twig_Hooks\Twig;

use Sylius\Twig_Hooks\Twig\Runtime\Hooks_Runtime;
use Sylius\Twig_Hooks\Twig\Token_Parser\Hook_Token_Parser;
use Twig\Extension\Abstract_Extension;
use Twig\Token_Parser\Token_Parser_Interface;
use Twig\Twig_Function;
final class Hooks_Extension extends Abstract_Extension
{
    public function get_functions(): array
    {
        return [new Twig_Function('get_hookable_metadata', [Hooks_Runtime::class, 'getHookableMetadata'], ['needs_context' => true]), new Twig_Function('get_hookable_context', [Hooks_Runtime::class, 'getHookableContext'], ['needs_context' => true]), new Twig_Function('get_hookable_configuration', [Hooks_Runtime::class, 'getHookableConfiguration'], ['needs_context' => true]), new Twig_Function('is_hookable', [Hooks_Runtime::class, 'isHookable'], ['needs_context' => true])];
    }
    /**
     * @return array<TokenParserInterface>
     */
    public function get_token_parsers(): array
    {
        return [new Hook_Token_Parser()];
    }
}