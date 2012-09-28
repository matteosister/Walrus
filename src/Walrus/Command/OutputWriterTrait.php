<?php
/**
 * User: matteo
 * Date: 28/09/12
 * Time: 16.15
 *
 * Just for fun...
 */

namespace Walrus\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\ArrayInput;

trait OutputWriterTrait
{
    private $commandSectionPad = 20;
    private $commandStringPad = '.';

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
        return sprintf($tpl, str_pad($section, $this->commandSectionPad, $this->commandStringPad, STR_PAD_RIGHT), $message);
    }

    protected function runProjectStartup(OutputInterface $output, Application $application)
    {
        $command = $application->find('startup:project');
        $arguments = array(
            'command' => 'startup:project'
        );
        $input = new ArrayInput($arguments);
        return 0 === $command->run($input, $output);
    }
}
