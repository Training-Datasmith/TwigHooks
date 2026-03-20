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
namespace Sylius\Twig_Hooks\Hook\Normalizer\Name;

final class Composite_Name_Normalizer implements Name_Normalizer_Interface
{
    /** @var array<NameNormalizerInterface> */
    private readonly array $name_normalizers;
    /**
     * @param iterable<NameNormalizerInterface> $nameNormalizers
     */
    public function __construct(iterable $name_normalizers)
    {
        $this->name_normalizers = $name_normalizers instanceof \Traversable ? iterator_to_array($name_normalizers) : $name_normalizers;
    }
    public function normalize(string $name): string
    {
        $normalized_hook_name = $name;
        foreach ($this->name_normalizers as $name_normalizer) {
            $normalized_hook_name = $name_normalizer->normalize($normalized_hook_name);
        }
        return $normalized_hook_name;
    }
}