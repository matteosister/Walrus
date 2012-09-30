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
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Process\Process,
    Symfony\Component\Process\ProcessBuilder;

use Walrus\Command\OutputWriterTrait,
    Walrus\DI\Configuration;

/**
 * startup php server
 */
class StartupServerCommand extends Command
{
    use OutputWriterTrait;

    /**
     * @var \Walrus\DI\Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        parent::__construct();
        $this->configuration = $configuration;
    }

    public function configure()
    {
        $this
            ->setName('startup:server')
            ->setDescription('Manage the php built-in web server')
            ->addOption('port', 'p', InputOption::VALUE_OPTIONAL, 'the server port', '8000');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->writeHeader($output);
        $cmd = sprintf('php -S localhost:%s', $input->getOption('port'));
        $builder = new ProcessBuilder();
        $builder->add('php');
        $builder->add('-S');
        $builder->add(sprintf('localhost:%s', $input->getOption('port')));
        $builder->setWorkingDirectory($this->configuration->get('public_dir'));
        $builder->setTimeout(null);
        $process = $builder->getProcess();
        $output->writeln(sprintf('<info>starting up walrus server, now open http://localhost:%s on your browser</info>', $input->getOption('port')));
        $process->run(function($type, $data) use ($output) {
            // [Mon Oct  1 00:31:20 2012] 127.0.0.1:50364 [200]: /
            if (preg_match('/\[(.+)\] (.+):(.+) \[(.+)\]: (.*)/', $data, $matches)) {
                $date = $matches[1];
                $ip = $matches[2];
                $port = $matches[3];
                $response = $matches[4];
                switch (substr($response, 0, 1)) {
                    case 2:
                        $type = 'info';
                        break;
                    case 3:
                        $type = 'comment';
                        break;
                    case 4:
                        $type = 'error';
                        break;
                    default:
                        $type = 'null';
                        break;
                }
                $resource = $matches[5];
                //var_dump($date, $ip, $port, $response, $resource);
                $output->writeln(sprintf('<comment>%s</comment> [<%s>%s</%s>]: %s', $date, $type, $response, $type, $resource));
            } else {
                $output->write($data);
            }
        });
        if (!$process->isSuccessful()) {
            $output->writeln('<error>There was an error launching your server</error>');
        }
    }
}
