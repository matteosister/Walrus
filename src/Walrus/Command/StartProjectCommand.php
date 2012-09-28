<?php
/**
 * User: matteo
 * Date: 28/09/12
 * Time: 17.42
 *
 * Just for fun...
 */

use Symfony\Component\Console\Command\Command,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Finder\Finder,
    Symfony\Component\Filesystem\Filesystem;

use Walrus\Command\OutputWriterTrait,
    Walrus\DI\Configuration;


class StartProjectCommand extends Command
{
    use OutputWriterTrait;

    /**
     * @var \Walrus\DI\Configuration
     */
    protected $configuration;

    public function __construct(
        Configuration $configuration
    )
    {
        parent::__construct();
        $this->configuration = $configuration;
    }

    protected function configure()
    {
        return $this
            ->setName('start:project')
            ->setDescription('Create the project folder structure');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fs = new Filesystem();
        $fs->copy(__DIR__.'/../Resources/defaults/config.yml', $this->configuration->get('root_dir'));
    }
}
