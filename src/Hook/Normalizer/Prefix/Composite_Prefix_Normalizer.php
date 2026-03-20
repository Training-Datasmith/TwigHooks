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
namespace Sylius\Twig_Hooks\Hook\Normalizer\Prefix;

final class Composite_Prefix_Normalizer implements Prefix_Normalizer_Interface
{
    /** @var array<PrefixNormalizerInterface> */
    private readonly array $prefix_normalizers;
    /**
     * @param iterable<PrefixNormalizerInterface> $prefixNormalizers
     */
    public function __construct(iterable $prefix_normalizers)
    {
        $this->prefix_normalizers = $prefix_normalizers instanceof \Traversable ? iterator_to_array($prefix_normalizers) : $prefix_normalizers;
    }
    public function normalize(string $prefix): string
    {
        $normalized_prefix = $prefix;
        foreach ($this->prefix_normalizers as $prefix_normalizer) {
            $normalized_prefix = $prefix_normalizer->normalize($normalized_prefix);
        }
        return $normalized_prefix;
    }
}