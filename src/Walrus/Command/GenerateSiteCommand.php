<?php
/**
 * User: matteo
 * Date: 29/08/12
 * Time: 16.45
 *
 * Just for fun...
 */

namespace Walrus\Command;

use Walrus\MDObject\Page\Page,
    Walrus\DI\Configuration,
    Walrus\Collection\PageCollection,
    Walrus\Asset\AssetCollection,
    Walrus\Asset\Project\AbstractProject,
    Walrus\Command\OutputWriterTrait;
use Symfony\Component\Console\Command\Command,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Finder\Finder,
    Symfony\Component\Filesystem\Filesystem;

/**
 * generate:site command
 */
class GenerateSiteCommand extends Command
{
    use OutputWriterTrait;

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
     * @var string
     */
    protected $previosWatch = null;

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
            ->addOption('watch', 'w', InputOption::VALUE_NONE, 'Check for changes every second')
            ->addOption('period', null, InputOption::VALUE_REQUIRED, 'Set the polling period in seconds (used with --watch)', 1)
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
        if ($input->getOption('watch')) {
            $this->watch($input, $output, true);
        } else {
            $this->doExecute($input, $output);
        }
    }

    private function doExecute(InputInterface $input, OutputInterface $output)
    {
        $this->writeRuler($output);
        $this->cleanup($output);
        $this->setup($output);
        $this->compileAssets($output);
        $this->parsePages($output);
    }

    private function watch(InputInterface $input, OutputInterface $output)
    {
        while (true) {
            $sha = $this->calculateSha();
            if ($sha !== $this->previosWatch) {
                $this->previosWatch = $sha;
                $this->doExecute($input, $output);
                $this->writeRuler($output);
                $output->writeln('<question>watching for changes...</question>');
            }
            sleep($input->getOption('period'));
        }
    }

    private function calculateSha()
    {
        $iterator = Finder::create()->files()->in($this->configuration->get('drafting_dir'));
        $content = '';
        foreach($iterator as $file) {
            $content .= $file->getContents();
        }
        return sha1($content);
    }

    private function cleanup(OutputInterface $output)
    {
        $output->writeln($this->getLine('cleaning', 'public folder'));
        $iterator = Finder::create()->files()->in($this->configuration->get('public_dir'));
        $fs = new Filesystem();
        $fs->remove($iterator);
    }

    private function setup(OutputInterface $output)
    {
        $output->writeln($this->getLine('warming up', 'public folder'));
        $cssDir = $this->configuration->get('public_dir').'/'.AbstractProject::TYPE_CSS;
        $jsDir = $this->configuration->get('public_dir').'/'.AbstractProject::TYPE_JS;
        $fs = new Filesystem();
        $fs->mkdir($cssDir);
        $fs->mkdir($jsDir);
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
        } catch (\Walrus\Exception\NoPagesCreated $e) {
            $output->writeln('<info>no pages created</info>');
        }
    }

    private function compileAssets(OutputInterface $output)
    {
        if (count($this->assetProjectsCollection) > 0) {
            $output->writeln($this->getLine('compiling', 'static assets (js/css)'));
            $this->assetProjectsCollection->compile($output, $this->configuration);
            /*foreach ($this->assetProjectsCollection as $assetProject) {
                $assetProject->compile();
                $output->writeln($this->getLine('compiling', sprintf('<comment>%s</comment> project', $assetProject->getName())));
                $assetProject->publish($this->configuration->get('public_dir').'/'.$assetProject->getProjectType());
                $output->writeln($this->getLine('publishing', sprintf('<comment>%s</comment> project', $assetProject->getName())));
            }*/
        } else {
            $output->writeln('<comment>No assets to compile</comment>');
        }
    }
}
