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
namespace Sylius\Twig_Hooks\Dependency_Injection;

use Sylius\Twig_Hooks\Hookable\Disabled_Hookable;
use Sylius\Twig_Hooks\Hookable\Hookable_Component;
use Sylius\Twig_Hooks\Hookable\Hookable_Template;
use Symfony\Component\Config\Definition\Builder\Array_Node_Definition;
use Symfony\Component\Config\Definition\Builder\Tree_Builder;
use Symfony\Component\Config\Definition\Configuration_Interface;
final class Configuration implements Configuration_Interface
{
    public function get_config_tree_builder(): Tree_Builder
    {
        $tree_builder = new Tree_Builder('sylius_twig_hooks');
        $root_node = $tree_builder->get_root_node();
        $root_node->children()->boolean_node('enable_autoprefixing')->default_false()->end()->scalar_node('hook_name_section_separator')->default_false()->end()->end()->end();
        $this->add_supported_hookable_types_configuration($root_node);
        $this->add_hooks_configuration($root_node);
        return $tree_builder;
    }
    private function add_supported_hookable_types_configuration(Array_Node_Definition $root_node): void
    {
        $root_node->children()->array_node('supported_hookable_types')->use_attribute_as_key('type')->default_value(['template' => Hookable_Template::class, 'component' => Hookable_Component::class, 'disabled' => Disabled_Hookable::class])->scalar_prototype()->end()->end()->end();
    }
    private function add_hooks_configuration(Array_Node_Definition $root_node): void
    {
        $root_node->children()->array_node('hooks')->use_attribute_as_key('_name')->array_prototype()->use_attribute_as_key('_name')->array_prototype()->before_normalization()->always(function (array $v): array {
            $is_component_defined = isset($v['component']);
            $is_template_defined = isset($v['template']);
            $is_disabled = isset($v['enabled']) && $v['enabled'] === false;
            if (!$is_component_defined && !$is_template_defined && !$is_disabled) {
                return $v;
            }
            $v['type'] = match (true) {
                $is_disabled => 'disabled',
                $is_component_defined => 'component',
                $is_template_defined => 'template',
                default => 'undefined',
            };
            return $v;
        })->end()->validate()->always(static function (array $v): array {
            $component = $v['component'] ?? null;
            $template = $v['template'] ?? null;
            $enabled = $v['enabled'] ?? true;
            if (null !== $component && null !== $template) {
                throw new \InvalidArgumentException('You cannot define both "component" and "template" at the same time.');
            }
            if ($enabled && null === $component && null === $template) {
                throw new \InvalidArgumentException('You must define either "component" or "template" for enabled hookables.');
            }
            if (null === $component && [] !== $v['props']) {
                throw new \InvalidArgumentException('"Props" cannot be defined for non-component hookables.');
            }
            return $v;
        })->end()->can_be_disabled()->children()->scalar_node('type')->default_null()->end()->scalar_node('component')->default_null()->end()->scalar_node('template')->default_null()->end()->array_node('context')->default_value([])->use_attribute_as_key('name')->prototype('variable')->end()->end()->array_node('props')->default_value([])->use_attribute_as_key('name')->prototype('variable')->end()->end()->array_node('configuration')->default_value([])->use_attribute_as_key('name')->prototype('variable')->end()->end()->integer_node('priority')->default_null()->end()->end()->end()->end()->end()->end();
    }
}