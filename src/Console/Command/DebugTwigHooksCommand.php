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
namespace Sylius\Twig_Hooks\Console\Command;

use Sylius\Twig_Hooks\Hookable\Abstract_Hookable;
use Sylius\Twig_Hooks\Hookable\Disabled_Hookable;
use Sylius\Twig_Hooks\Hookable\Hookable_Component;
use Sylius\Twig_Hooks\Hookable\Hookable_Template;
use Sylius\Twig_Hooks\Registry\Hookables_Registry;
use Symfony\Component\Console\Attribute\As_Command;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Completion\Completion_Input;
use Symfony\Component\Console\Completion\Completion_Suggestions;
use Symfony\Component\Console\Input\Input_Argument;
use Symfony\Component\Console\Input\Input_Interface;
use Symfony\Component\Console\Input\Input_Option;
use Symfony\Component\Console\Output\Output_Interface;
use Symfony\Component\Console\Style\Symfony_Style;
use Symfony\Component\Var_Exporter\Var_Exporter;
#[As_Command(name: 'sylius:debug:twig-hooks', description: 'Debug twig hooks configuration.')]
final class Debug_Twig_Hooks_Command extends Command
{
    public function __construct(private readonly Hookables_Registry $hookables_registry)
    {
        parent::__construct();
    }
    protected function configure(): void
    {
        $this->set_definition([new Input_Argument('name', Input_Argument::OPTIONAL, 'A hook name or part of the hook name'), new Input_Option('all', 'a', Input_Option::VALUE_NONE, 'Show all hookables including disabled ones'), new Input_Option('config', 'c', Input_Option::VALUE_NONE, 'Show hookables configuration')])->set_help(<<<'EOF'
        The <info>%command.name%</info> displays all Twig hooks in your application.
        
        To list all hooks:
        
            <info>php %command.full_name%</info>
        
        To filter hooks by name:
        
            <info>php %command.full_name% sylius_admin</info>
        
        To get specific information about a hook:
        
            <info>php %command.full_name% sylius_admin.product.index</info>
        
        To include disabled hookables:
        
            <info>php %command.full_name% sylius_admin.product.index --all</info>
        
        To show hookables configuration:
        
            <info>php %command.full_name% sylius_admin.product.index --config</info>
        EOF);
    }
    public function complete(Completion_Input $input, Completion_Suggestions $suggestions): void
    {
        if ($input->must_suggest_argument_values_for('name')) {
            $suggestions->suggest_values($this->hookables_registry->get_hook_names());
        }
    }
    protected function execute(Input_Interface $input, Output_Interface $output): int
    {
        $io = new Symfony_Style($input, $output);
        $name = $input->get_argument('name');
        /** @var bool $showAll */
        $show_all = $input->get_option('all');
        /** @var bool $showConfig */
        $show_config = $input->get_option('config');
        $hook_names = $this->hookables_registry->get_hook_names();
        sort($hook_names);
        if (\is_string($name)) {
            // Exact match - show details
            if (\in_array($name, $hook_names, true)) {
                $this->display_hook_details($io, $name, $show_all, $show_config);
                return Command::SUCCESS;
            }
            // Partial match - filter and show table or details (case-insensitive)
            $filtered_hooks = array_filter($hook_names, static fn(string $hook_name): bool => false !== stripos($hook_name, $name));
            if (0 === \count($filtered_hooks)) {
                $io->warning(\sprintf('No hooks found matching "%s".', $name));
                return Command::SUCCESS;
            }
            if (1 === \count($filtered_hooks)) {
                $this->display_hook_details($io, reset($filtered_hooks), $show_all, $show_config);
                return Command::SUCCESS;
            }
            $this->display_hooks_table($io, $filtered_hooks, $show_all);
            return Command::SUCCESS;
        }
        if (0 === \count($hook_names)) {
            $io->warning('No hooks registered.');
            return Command::SUCCESS;
        }
        $this->display_hooks_table($io, $hook_names, $show_all);
        return Command::SUCCESS;
    }
    /**
     * @param array<string> $hookNames
     */
    private function display_hooks_table(Symfony_Style $io, array $hook_names, bool $show_all): void
    {
        $rows = [];
        foreach ($hook_names as $hook_name) {
            $hookables = $this->hookables_registry->get_for($hook_name);
            $enabled_count = \count(array_filter($hookables, static fn(Abstract_Hookable $hookable): bool => !$hookable instanceof Disabled_Hookable));
            $disabled_count = \count($hookables) - $enabled_count;
            $count_display = $show_all && $disabled_count > 0 ? \sprintf('%d (%d disabled)', \count($hookables), $disabled_count) : (string) $enabled_count;
            $rows[] = [$hook_name, $count_display];
        }
        $io->table(['Hook', 'Hookables'], $rows);
        $io->text(\sprintf('Total: %d hooks', \count($hook_names)));
    }
    private function display_hook_details(Symfony_Style $io, string $hook_name, bool $show_all, bool $show_config): void
    {
        $io->title($hook_name);
        $hookables = $this->hookables_registry->get_for($hook_name);
        if (!$show_all) {
            $hookables = array_filter($hookables, static fn(Abstract_Hookable $hookable): bool => !$hookable instanceof Disabled_Hookable);
        }
        if (0 === \count($hookables)) {
            $io->warning('No hookables registered for this hook.');
            return;
        }
        $headers = ['Name', 'Type', 'Target', 'Priority'];
        if ($show_all) {
            $headers[] = 'Status';
        }
        if ($show_config) {
            $headers[] = 'Configuration';
        }
        $rows = [];
        foreach ($hookables as $hookable) {
            $row = [$hookable->name, $this->get_hookable_type($hookable), $this->get_hookable_target($hookable), $hookable->priority()];
            if ($show_all) {
                $row[] = $hookable instanceof Disabled_Hookable ? 'disabled' : 'enabled';
            }
            if ($show_config) {
                $row[] = $this->format_configuration($hookable->configuration);
            }
            $rows[] = $row;
        }
        $io->table($headers, $rows);
    }
    /**
     * @param array<string, mixed> $configuration
     */
    private function format_configuration(array $configuration): string
    {
        if (0 === \count($configuration)) {
            return '-';
        }
        return Var_Exporter::export($configuration);
    }
    private function get_hookable_type(Abstract_Hookable $hookable): string
    {
        return match (true) {
            $hookable instanceof Hookable_Template => 'template',
            $hookable instanceof Hookable_Component => 'component',
            default => '-',
        };
    }
    private function get_hookable_target(Abstract_Hookable $hookable): string
    {
        return match (true) {
            $hookable instanceof Hookable_Template => $hookable->template,
            $hookable instanceof Hookable_Component => $hookable->component,
            default => '-',
        };
    }
}