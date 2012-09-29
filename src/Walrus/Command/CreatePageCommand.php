<?php
/**
 * User: matteo
 * Date: 26/08/12
 * Time: 22.56
 *
 * Just for fun...
 */

namespace Walrus\Command;

use Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Console\Command\Command;
use Walrus\Utilities\Utilities,
    Walrus\DI\Configuration,
    Walrus\Collection\PageCollection,
    Walrus\Collection\Collection,
    Walrus\MDObject\Page\Page;

/**
 * create:page command
 */
class CreatePageCommand extends Command
{
    use OutputWriterTrait;

    const NAME = 'page';

    /**
     * @var \Twig_Environment
     */
    protected $twigEnvironment;

    /**
     * @var \Walrus\Utilities\Utilities
     */
    protected $utilities;

    /**
     * @var string
     */
    private $pagesFolder;

    /**
     * @var
     */
    private $pageCollection;

    /**
     * constructor
     *
     * @param \Walrus\DI\Configuration    $configuration configuration
     * @param \Twig_Environment           $environment   environment
     * @param \Walrus\Utilities\Utilities $utilities     utilities
     */
    public function __construct(
        Configuration $configuration,
        \Twig_Environment $environment,
        Utilities $utilities,
        PageCollection $pageCollection
    )
    {
        parent::__construct();
        $this->configuration = $configuration;
        $this->twigEnvironment = $environment;
        $this->utilities = $utilities;
        $this->pagesFolder = $this->configuration->get('drafting_dir').'/'.static::NAME.'s';
        $this->pageCollection = $pageCollection;
    }

    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->setName('create:page')
            ->setDescription('Create a new page')
            ->addOption('homepage', null, InputOption::VALUE_NONE, 'The page is the homepage')
            ->addArgument('title', InputArgument::REQUIRED, 'The title of the page')
            ->addArgument('parent', InputArgument::OPTIONAL, 'the parent page', false);
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
        $this->writeHeader($output);
        if (!is_dir($this->pagesFolder)) {
            /*$this->writeRuler($output);
            $output->writeln('<error>looks like you didn\'t startup your project...</error>');
            $output->writeln('You need to run the <info>startup:project</info> command');
            $this->writeRuler($output);
            $dialog = $this->getHelperSet()->get('dialog');
            if (!$dialog->askConfirmation($output, '<question>Would you like to run it now? [y/n]</question>', false)) {
                return;
            } else {
                $this->runProjectStartup($output, $this->getApplication());
            }*/
            $this->runProjectStartup($output, $this->getApplication());
        }
        $date = $this->utilities->getDateFormatted();
        $title = $input->getArgument('title');
        $slug = $this->utilities->getUniqueSlug(array_map(function(Page $p) {
            return $p->getMetadata()->getUrl();
        }, $this->pageCollection->toArray()), $title);
        $template = $this->twigEnvironment->loadTemplate(static::NAME.'.md.twig');
        $homepage = $input->getOption('homepage') ? true : false;
        $fileContent = $template->render(array(
            'title' => $title,
            'date' => $date,
            'url' => $input->getOption('homepage') ? '' : $slug,
            'homepage' => 0 === $this->pageCollection->count() ? true : $homepage
        ));
        $dir = $this->pagesFolder;
        if (!is_dir($dir)) {
            mkdir($dir);
        }
        $fileName = $dir.'/'.$date.'_'.static::NAME.'_'.$slug.'.md';
        file_put_contents($fileName, $fileContent);
        $this->writeRuler($output);
        $output->writeln(sprintf('<info>Page</info> <comment>%s</comment> created', $title));
    }
}
