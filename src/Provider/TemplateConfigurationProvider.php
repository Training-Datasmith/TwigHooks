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
namespace Sylius\Twig_Hooks\Provider;

use Sylius\Twig_Hooks\Hookable\Hookable_Template;
use Sylius\Twig_Hooks\Hookable\Metadata\Hookable_Metadata;
use Sylius\Twig_Hooks\Provider\Exception\Invalid_Expression_Exception;
use Symfony\Component\Expression_Language\Expression_Language;
final class Template_Configuration_Provider implements Template_Configuration_Provider_Interface
{
    public function __construct(private readonly Expression_Language $expression_language)
    {
    }
    public function provide(Hookable_Template $hookable, Hookable_Metadata $metadata): array
    {
        $values = ['_context' => $metadata->context];
        return $this->map_array_recursively(function (mixed $value) use ($values, $hookable): mixed {
            if (is_string($value) && str_starts_with($value, '@=')) {
                try {
                    return $this->expression_language->evaluate(substr($value, 2), $values);
                } catch (\Throwable $e) {
                    throw new Invalid_Expression_Exception(sprintf('Failed to evaluate the "%s" expression while rendering the "%s" hookable in the "%s" hook. Error: %s".', $value, $hookable->name, $hookable->hook_name, $e->get_message()), previous: $e);
                }
            }
            return $value;
        }, $hookable->configuration);
    }
    /**
     * @param array<array-key, mixed> $array
     *
     * @return array<array-key, mixed>
     */
    private function map_array_recursively(callable $callback, array $array): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            $result[$key] = is_array($value) ? $this->map_array_recursively($callback, $value) : $callback($value);
        }
        return $result;
    }
}