<?php
/**
 * User: matteo
 * Date: 29/08/12
 * Time: 16.45
 *
 * Just for fun...
 */

namespace Walrus\Command;

use Walrus\Command\BaseCommand,
    Walrus\MDObject\Post\Post,
    Walrus\MDObject\Page\Page,
    Walrus\DI\Configuration,
    Walrus\Collection\PageCollection,
    Walrus\Collection\PostCollection,
    Walrus\Collection\Collection,
    Walrus\Asset\AssetCollection;
use LessElephant\LessProject;
use CompassElephant\CompassProject;
use Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Finder\Finder,
    Symfony\Component\Filesystem\Filesystem;

/**
 * generate:site command
 */
class GenerateSiteCommand extends BaseCommand
{
    /**
     * @var \Walrus\DI\Configuration
     */
    protected $configuration;

    /**
     * @var \Twig_Environment
     */
    protected $twigEnvironment;

    /**
     * @var \Twig_Environment
     */
    protected $themeEnvironment;

    /**
     * @var \Twig_Environment
     */
    protected $stringEnvironment;

    /**
     * @var \Walrus\Asset\Collection
     */
    protected $assetProjectsCollection;

    /**
     * @var PageCollection
     */
    protected $pageCollection;

    /**
     * constructor
     *
     * @param \Walrus\DI\Configuration $configuration          configuration
     * @param \Twig_Environment        $environment            twig environment
     * @param \Twig_Environment        $themeEnvironment       twig environment for the theme
     * @param \Twig_Environment        $stringEnvironment      twig environment from string
     * @param \Walrus\Asset\Collection $assetProjectCollection less project
     */
    public function __construct(
        Configuration $configuration,
        \Twig_Environment $environment,
        \Twig_Environment $themeEnvironment,
        \Twig_Environment $stringEnvironment,
        AssetCollection $assetProjectCollection,
        PageCollection $pageCollection
    )
    {
        parent::__construct();
        $this->configuration = $configuration;
        $this->twigEnvironment = $environment;
        $this->themeEnvironment = $themeEnvironment;
        $this->stringEnvironment = $stringEnvironment;
        $this->assetProjectsCollection = $assetProjectCollection;
        $this->pageCollection = $pageCollection;
    }

    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->setName('generate:site')
            ->setDescription('Generate the website');
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
        $this->cleanup($output);
        $this->compileAssets($output);
        $this->parsePages($output);
    }

    private function cleanup(OutputInterface $output)
    {
        $output->writeln($this->getLine('cleaning', 'public folder'));
        $iterator = Finder::create()->files()->in($this->configuration->get('public_dir'));
        $fs = new Filesystem();
        $fs->remove($iterator);
        //$output->writeln($this->getDone('cleaning'));
    }

    /**
     * parse pages
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output output
     *
     * @return void
     */
    private function parsePages(OutputInterface $output)
    {
        try {
            if (count($this->pageCollection) == 0) {
                $output->writeln('<info>No pages to generate</info>');

                return;
            }
            $output->writeln($this->getLine('generating', sprintf('%s page/s', count($this->pageCollection))));
            foreach ($this->pageCollection as $page) {
                if ($page->getMetadata()->getHomepage()) {
                    $url = 'index.html';
                } else {
                    $url = $page->getMetadata()->getUrl().'.html';
                }
                $filename = $this->configuration->get('public_dir').'/'.$url;
                if (file_exists($filename)) {
                    unlink($filename);
                }
                file_put_contents($filename, $this->themeEnvironment->render('page.html.twig', array(
                    'page' => $page,
                    'content' => $this->stringEnvironment->render($page->getContent())
                )));
                $output->writeln($this->getLine('generating page', sprintf('<comment>%s</comment>', $page->getMetadata()->getTitle())));
            }
            //$output->writeln($this->getDone('generating pages'));
        } catch (\Walrus\Exception\NoPagesCreated $e) {
            $output->writeln('<info>no pages created</info>');
        }
    }

    private function compileAssets(OutputInterface $output)
    {
        if (count($this->assetProjectsCollection) > 0) {
            $output->writeln($this->getLine('compiling', 'static assets (js/css)'));
            foreach ($this->assetProjectsCollection as $assetProject) {
                $assetProject->compile();
                $output->writeln($this->getLine('compiling', sprintf('<comment>%s</comment> project', $assetProject->getName())));
                $assetProject->publish($this->configuration->get('public_dir').'/css');
                $output->writeln($this->getLine('publishing', sprintf('<comment>%s</comment> project', $assetProject->getName())));
            }
        } else {
            $output->writeln('<comment>No assets to compile</comment>');
        }
    }
}
