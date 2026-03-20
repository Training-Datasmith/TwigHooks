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
namespace Sylius\Twig_Hooks\Live_Component;

use Sylius\Twig_Hooks\Bag\Data_Bag;
use Sylius\Twig_Hooks\Bag\Scalar_Data_Bag;
use Sylius\Twig_Hooks\Hook\Metadata\Hook_Metadata;
use Sylius\Twig_Hooks\Hookable\Metadata\Hookable_Metadata;
use Symfony\UX\Live_Component\Attribute\Live_Prop;
use Symfony\UX\Twig_Component\Attribute\Expose_In_Template;
trait Hookable_Live_Component_Trait
{
    #[Live_Prop(hydrateWith: 'hydrateHookableMetadata', dehydrateWith: 'dehydrateHookableMetadata')]
    #[Expose_In_Template('hookable_metadata')]
    public ?Hookable_Metadata $hookable_metadata = null;
    public function hydrate_hookable_metadata(array $data): ?Hookable_Metadata
    {
        if (null === $data) {
            return null;
        }
        return new Hookable_Metadata(new Hook_Metadata($data['renderedBy'], new Data_Bag()), new Data_Bag(), new Scalar_Data_Bag(json_decode((string) $data['configuration'], true)), $data['prefixes'] ?? []);
    }
    public function dehydrate_hookable_metadata(?Hookable_Metadata $metadata = null): ?array
    {
        if (null === $metadata) {
            return null;
        }
        return ['renderedBy' => $metadata->rendered_by->name, 'configuration' => json_encode($metadata->configuration->all()), 'prefixes' => $metadata->prefixes];
    }
}