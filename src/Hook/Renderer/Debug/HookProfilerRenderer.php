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

use Sylius\Twig_Hooks\Hook\Renderer\Hook_Renderer_Interface;
use Sylius\Twig_Hooks\Profiler\Profile;
use Symfony\Component\Stopwatch\Stopwatch;
final class Hook_Profiler_Renderer implements Hook_Renderer_Interface
{
    public function __construct(private readonly Hook_Renderer_Interface $inner_renderer, private readonly ?Profile $profile, private readonly ?Stopwatch $stopwatch)
    {
    }
    public function render(array $hook_names, array $hook_context = []): string
    {
        $this->profile?->register_hook_start($hook_names);
        $this->stopwatch?->start(md5(serialize($hook_names)));
        $rendered = $this->inner_renderer->render($hook_names, $hook_context);
        $this->profile?->register_hook_end($this->stopwatch?->stop(md5(serialize($hook_names)))->get_duration());
        return $rendered;
    }
}