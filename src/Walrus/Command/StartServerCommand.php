<?php
/**
 * User: matteo
 * Date: 30/09/12
 * Time: 23.42
 *
 * Just for fun...
 */

namespace Walrus\Command;

use Symfony\Component\Console\Command\Command,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Console\Input\InputOption;
use Walrus\Command\OutputWriterTrait;

class StartServerCommand extends Command
{
    use OutputWriterTrait;

    public function configure()
    {
        $this
            ->setName('start:server')
            ->addOption('port', 'p', InputOption::VALUE_OPTIONAL, 'the server port', '8000');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }
}
