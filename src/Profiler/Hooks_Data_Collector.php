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
namespace Sylius\Twig_Hooks\Profiler;

use Sylius\Twig_Hooks\Profiler\Dumper\Html_Dumper;
use Symfony\Component\Http_Kernel\Data_Collector\Data_Collector;
use Symfony\Component\Http_Kernel\Data_Collector\Late_Data_Collector_Interface;
use Twig\Markup;
/** @internal */
final class Hooks_Data_Collector extends Data_Collector implements Late_Data_Collector_Interface
{
    public function __construct(private Profile $profile)
    {
    }
    public function get_name(): string
    {
        return 'sylius_twig_hooks';
    }
    public function late_collect(): void
    {
        $this->data = ['profile' => serialize($this->profile)];
    }
    private function get_profile(): Profile
    {
        return $this->profile ??= unserialize($this->data['profile'], ['allowed_classes' => [Profile::class]]);
    }
    public function get_total_duration(): string
    {
        return sprintf('%.1f', $this->get_profile()->get_total_duration());
    }
    public function get_number_of_hooks(): int
    {
        return $this->get_profile()->get_number_of_hooks();
    }
    public function get_number_of_hookables(): int
    {
        return $this->get_profile()->get_number_of_hookables();
    }
    public function get_call_graph(): Markup
    {
        $dump = (new Html_Dumper())->dump($this->get_profile());
        return new Markup($dump, 'UTF-8');
    }
    public function reset(): void
    {
        $this->profile->reset();
        $this->data = [];
    }
}