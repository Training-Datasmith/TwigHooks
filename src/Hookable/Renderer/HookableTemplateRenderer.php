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

use Sylius\Twig_Hooks\Bag\Scalar_Data_Bag;
use Sylius\Twig_Hooks\Hookable\Abstract_Hookable;
use Sylius\Twig_Hooks\Hookable\Hookable_Template;
use Sylius\Twig_Hooks\Hookable\Metadata\Hookable_Metadata;
use Sylius\Twig_Hooks\Hookable\Renderer\Exception\Hook_Render_Exception;
use Sylius\Twig_Hooks\Provider\Template_Configuration_Provider_Interface;
use Sylius\Twig_Hooks\Twig\Runtime\Hooks_Runtime;
use Twig\Environment as Twig;
final class Hookable_Template_Renderer implements Supportable_Hookable_Renderer_Interface
{
    public function __construct(private readonly Twig $twig, private readonly Template_Configuration_Provider_Interface $configuration_provider)
    {
    }
    /**
     * @param HookableTemplate $hookable
     */
    public function render(Abstract_Hookable $hookable, Hookable_Metadata $metadata): string
    {
        if (!$this->supports($hookable)) {
            throw new \InvalidArgumentException(sprintf('Hookable must be the "%s", but "%s" given.', Hookable_Template::class, $hookable::class));
        }
        try {
            $configuration = $this->configuration_provider->provide($hookable, $metadata);
            $metadata = $metadata->with_configuration(new Scalar_Data_Bag($configuration));
            return $this->twig->render($hookable->template, [Hooks_Runtime::HOOKABLE_METADATA => $metadata]);
        } catch (\Throwable $exception) {
            throw new Hook_Render_Exception(sprintf('An error occurred during rendering the "%s" hook in the "%s" hookable. %s', $hookable->name, $hookable->hook_name, $exception->get_message()), previous: $exception);
        }
    }
    public function supports(Abstract_Hookable $hookable): bool
    {
        return is_a($hookable, Hookable_Template::class, true);
    }
}