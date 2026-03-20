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
namespace Sylius\Twig_Hooks\Hook\Renderer;

use Sylius\Twig_Hooks\Bag\Data_Bag;
use Sylius\Twig_Hooks\Bag\Scalar_Data_Bag;
use Sylius\Twig_Hooks\Hook\Metadata\Hook_Metadata;
use Sylius\Twig_Hooks\Hookable\Metadata\Hookable_Metadata_Factory_Interface;
use Sylius\Twig_Hooks\Hookable\Renderer\Hookable_Renderer_Interface;
use Sylius\Twig_Hooks\Provider\Configuration_Provider_Interface;
use Sylius\Twig_Hooks\Provider\Context_Provider_Interface;
use Sylius\Twig_Hooks\Registry\Hookables_Registry;
final class Hook_Renderer implements Hook_Renderer_Interface
{
    public function __construct(private readonly Hookables_Registry $hookables_registry, private readonly Hookable_Renderer_Interface $composite_hookable_renderer, private readonly Context_Provider_Interface $context_provider, private readonly Configuration_Provider_Interface $configuration_provider, private readonly Hookable_Metadata_Factory_Interface $hookable_metadata_factory)
    {
    }
    /**
     * @param array<string> $hookNames
     * @param array<string, mixed> $hookContext
     */
    public function render(array $hook_names, array $hook_context = []): string
    {
        $hookables = $this->hookables_registry->get_enabled_for($hook_names);
        $rendered_hookables = [];
        foreach ($hookables as $hookable) {
            $hook_metadata = new Hook_Metadata($hookable->hook_name, new Data_Bag($hook_context));
            $context = $this->context_provider->provide($hookable, $hook_context);
            $configuration = $this->configuration_provider->provide($hookable);
            $hookable_metadata = $this->hookable_metadata_factory->create($hook_metadata, new Data_Bag($context), new Scalar_Data_Bag($configuration), $hook_names);
            $rendered_hookables[] = $this->composite_hookable_renderer->render($hookable, $hookable_metadata);
        }
        return implode(\PHP_EOL, $rendered_hookables);
    }
}