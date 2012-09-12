<?php
/**
 * User: matteo
 * Date: 26/08/12
 * Time: 22.56
 *
 * Just for fun...
 */

namespace Walrus\Command;

use Walrus\Command\BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Walrus\Utilities\Utilities;
use Walrus\DI\Configuration;

/**
 * create:post command
 */
class CreatePostCommand extends BaseCommand
{
    const NAME = 'post';

    /**
     * @var \Twig_Environment
     */
    protected $twigEnvironment;

    /**
     * @var \Walrus\Utilities\Utilities
     */
    protected $utilities;

    /**
     * constructor
     *
     * @param \Walrus\DI\Configuration    $configuration configuration
     * @param \Twig_Environment           $environment   environment
     * @param \Walrus\Utilities\Utilities $utilities     utilities
     */
    public function __construct(Configuration $configuration, \Twig_Environment $environment, Utilities $utilities)
    {
        parent::__construct();
        $this->configuration = $configuration;
        $this->twigEnvironment = $environment;
        $this->utilities = $utilities;
    }

    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->setName('create:post')
            ->setDescription('Create a new blog post')
            ->addArgument('title', InputArgument::REQUIRED, 'The title of the post');
    }

    /**
     * execute
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input  input
     * @param \Symfony\Component\Console\Output\OutputInterface $output output
     *
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $date = $this->utilities->getDateFormatted();
        $title = $input->getArgument('title');
        $slug = $this->utilities->slugify($title);
        $this->writeHeader($output);
        $template = $this->twigEnvironment->loadTemplate(static::NAME.'.md.twig');
        $fileContent = $template->render(array(
            'title' => $title,
            'date' => $date,
            'url' => $slug
        ));
        $dir = $this->configuration->drafing_dir.'/'.static::NAME.'s';
        if (!is_dir($dir)) {
            mkdir($dir);
        }
        $fileName = $dir.'/'.$date.'_'.static::NAME.'_'.$slug.'.md';
        file_put_contents($fileName, $fileContent);
        $this->writeRuler($output);
        $output->writeln(sprintf('<info>Blog post "%s" created</info>', $title));
    }
}
