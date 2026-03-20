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
namespace Sylius\Twig_Hooks\Hookable\Renderer;

use Sylius\Twig_Hooks\Hookable\Abstract_Hookable;
use Sylius\Twig_Hooks\Hookable\Metadata\Hookable_Metadata;
use Sylius\Twig_Hooks\Hookable\Renderer\Exception\No_Supported_Renderer_Exception;
final class Composite_Hookable_Renderer implements Hookable_Renderer_Interface
{
    /** @var array<SupportableHookableRendererInterface> */
    private array $renderers = [];
    /**
     * @param iterable<object> $renderers
     */
    public function __construct(iterable $renderers)
    {
        foreach ($renderers as $renderer) {
            if (!$renderer instanceof Supportable_Hookable_Renderer_Interface) {
                throw new \InvalidArgumentException(sprintf('Hookable renderer must be an instance of "%s".', Supportable_Hookable_Renderer_Interface::class));
            }
            $this->renderers[] = $renderer;
        }
    }
    public function render(Abstract_Hookable $hookable, Hookable_Metadata $metadata): string
    {
        foreach ($this->renderers as $renderer) {
            if ($renderer->supports($hookable)) {
                return $renderer->render($hookable, $metadata);
            }
        }
        throw new No_Supported_Renderer_Exception($hookable->hook_name, $hookable->name);
    }
}