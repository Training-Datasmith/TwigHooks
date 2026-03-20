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

use Sylius\Twig_Hooks\Hookable\Abstract_Hookable;
use Sylius\Twig_Hooks\Hookable\Metadata\Hookable_Metadata;
use Sylius\Twig_Hooks\Hookable\Renderer\Hookable_Renderer_Interface;
use Sylius\Twig_Hooks\Profiler\Profile;
use Symfony\Component\Stopwatch\Stopwatch;
final class Hookable_Profiler_Renderer implements Hookable_Renderer_Interface
{
    public function __construct(private readonly Hookable_Renderer_Interface $inner_renderer, private readonly ?Profile $profile, private readonly ?Stopwatch $stopwatch)
    {
    }
    public function render(Abstract_Hookable $hookable, Hookable_Metadata $metadata): string
    {
        $this->profile?->register_hookable_render_start($hookable);
        $this->stopwatch?->start($hookable->id);
        $rendered = $this->inner_renderer->render($hookable, $metadata);
        $this->profile?->register_hookable_render_end($this->stopwatch?->stop($hookable->id)->get_duration());
        return $rendered;
    }
}