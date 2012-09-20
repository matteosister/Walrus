<?php
/**
 * User: matteo
 * Date: 27/08/12
 * Time: 23.26
 *
 * Just for fun...
 */

namespace Walrus\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * base class for commands
 */
abstract class BaseCommand extends Command
{
    const COMMAND_SECTION_PAD = 20;
    const COMMAND_STRING_PAD = '.';

    /**
     * @var \Walrus\DI\Configuration
     */
    protected $configuration;

    /**
     * write the console header
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output output
     */
    protected function writeHeader(OutputInterface $output)
    {
        $output->writeln("       ___");
        $output->writeln("    .-0 0 `\\");
        $output->writeln("  =(:(::)=  ;");
        $output->writeln("    ||||     \\");
        $output->writeln("    ||||   <info>I am the</info> <comment>Walrus</comment>");
        $output->writeln("   ,\|\|         `,");
        $output->writeln('');
    }

    /**
     * write a ruler
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output output
     */
    protected function writeRuler(OutputInterface $output)
    {
        $output->writeln('');
    }

    protected function getLine($section, $message, $comment = false)
    {
        $tpl = $comment ? '<info>%s</info> <comment>%s</comment>' : '<info>%s</info> %s';
        return sprintf($tpl, str_pad($section, static::COMMAND_SECTION_PAD, static::COMMAND_STRING_PAD, STR_PAD_RIGHT), $message);
    }

    protected function getDone($section)
    {
        return sprintf('<info>%s</info> <comment>done</comment>', str_pad($section, static::COMMAND_SECTION_PAD, static::COMMAND_STRING_PAD, STR_PAD_RIGHT));
    }
}
