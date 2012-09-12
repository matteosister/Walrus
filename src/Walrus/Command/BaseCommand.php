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
        $output->writeln("    ||||   <info>I am the Walrus</info>");
        $output->writeln("   ,\|\|         `,");
        $output->writeln("  /                \\");
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
}
