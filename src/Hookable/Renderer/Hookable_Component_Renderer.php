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
use Sylius\Twig_Hooks\Hookable\Hookable_Component;
use Sylius\Twig_Hooks\Hookable\Metadata\Hookable_Metadata;
use Sylius\Twig_Hooks\Provider\Exception\Invalid_Expression_Exception;
use Sylius\Twig_Hooks\Provider\Props_Provider_Interface;
use Symfony\UX\Twig_Component\Component_Renderer_Interface;
final class Hookable_Component_Renderer implements Supportable_Hookable_Renderer_Interface
{
    public const HOOKABLE_METADATA_PARAMETER = 'hookableMetadata';
    public function __construct(private readonly Props_Provider_Interface $props_provider, private readonly Component_Renderer_Interface $component_renderer)
    {
    }
    /**
     * @param HookableComponent $hookable
     *
     * @throws InvalidExpressionException
     */
    public function render(Abstract_Hookable $hookable, Hookable_Metadata $metadata): string
    {
        if (!$this->supports($hookable)) {
            throw new \InvalidArgumentException(sprintf('Hookable must be the "%s", but "%s" given.', Hookable_Component::class, $hookable::class));
        }
        $props = $this->props_provider->provide($hookable, $metadata);
        return $this->component_renderer->create_and_render($hookable->component, [self::HOOKABLE_METADATA_PARAMETER => $metadata, ...$props]);
    }
    public function supports(Abstract_Hookable $hookable): bool
    {
        return is_a($hookable, Hookable_Component::class, true);
    }
}