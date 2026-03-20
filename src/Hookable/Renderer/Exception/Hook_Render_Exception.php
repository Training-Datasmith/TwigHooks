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
namespace Sylius\Twig_Hooks\Hookable\Renderer\Exception;

use Twig\Error\Error;
use Twig\Error\Runtime_Error;
use Twig\Source;
class Hook_Render_Exception extends Runtime_Error
{
    public function __construct(string $message, ?int $lineno = null, ?Source $source = null, ?\Throwable $previous = null)
    {
        $lineno ??= $previous?->get_line() ?? -1;
        $source ??= $previous instanceof Error ? $previous->get_source_context() : null;
        parent::__construct($message, $lineno, $source, $previous);
    }
}