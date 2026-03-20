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

final class Remove_Section_Part_Normalizer implements Prefix_Normalizer_Interface
{
    /**
     * @param non-empty-string|false $separator
     */
    public function __construct(private readonly string|false $separator)
    {
    }
    public function normalize(string $prefix): string
    {
        if (false === $this->separator) {
            return $prefix;
        }
        $parts = explode('.', $prefix);
        $result = [];
        foreach ($parts as $part) {
            $hook_name_exploded_by_section_separator = explode($this->separator, $part);
            $result[] = current($hook_name_exploded_by_section_separator);
        }
        return implode('.', $result);
    }
}