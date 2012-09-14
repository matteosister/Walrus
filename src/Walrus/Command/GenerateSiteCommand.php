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
    Walrus\Collection\Collection;
use LessElephant\LessProject;
use CompassElephant\CompassProject;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

/**
 * generate:site command
 */
class GenerateSiteCommand extends BaseCommand
{
    /**
     * @var \Twig_Environment
     */
    protected $twigEnvironment;

    /**
     * @var \Twig_Environment
     */
    protected $themeEnvironment;

    /**
     * @var \Walrus\Asset\Collection
     */
    protected $assetProjectsCollection;

    /**
     * constructor
     *
     * @param \Walrus\DI\Configuration        $configuration    configuration
     * @param \Twig_Environment               $environment      twig environment
     * @param \Twig_Environment               $themeEnvironment twig environment for the theme
     * @param \LessElephant\LessProject       $lessProject      less project
     * @param \CompassElephant\CompassProject $compassProject   compass project
     */
    public function __construct(
        Configuration $configuration,
        \Twig_Environment $environment,
        \Twig_Environment $themeEnvironment,
        \Walrus\Asset\Collection $assetProjectCollection
    )
    {
        parent::__construct();
        $this->configuration = $configuration;
        $this->twigEnvironment = $environment;
        $this->themeEnvironment = $themeEnvironment;
        $this->assetProjectsCollection = $assetProjectCollection;
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
        $this->compileAssets($output);
        $this->parsePages($output);
        $this->parsePosts($output);
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
        $dir = $this->configuration->drafing_dir.'/pages';
        $pageCollection = new PageCollection(Collection::TYPE_PAGES);
        try {
            $pageCollection->load($dir);
            if (count($pageCollection) == 0) {
                $output->writeln('<info>No pages to generate</info>');

                return;
            }
            $output->writeln(sprintf('<info>Generating</info> %s page/s', count($pageCollection)));
            foreach ($pageCollection as $page) {
                $url = $page->getMetadata()->getUrl().'.html';
                $filename = $this->configuration->get('public_dir').'/'.$url;
                if (file_exists($filename)) {
                    unlink($filename);
                }
                file_put_contents($filename, $this->themeEnvironment->render('page.html.twig', array(
                    'page' => $page
                )));
            }
            $output->writeln(sprintf('<info>Generating</info> %s page/s <comment>done</comment>', count($pageCollection)));
        } catch (\Walrus\Exception\NoPagesCreated $e) {
            $output->writeln('<info>no pages created...</info>');
        }
    }

    /**
     * parse posts
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output output
     *
     * @return void
     */
    private function parsePosts(OutputInterface $output)
    {
        $dir = $this->configuration->drafing_dir.'/posts';
        if (!is_dir($dir)) {
            $output->writeln('<info>no posts created...</info>');

            return;
        }
        $finder = new Finder();
        $posts = $finder->files()->in($this->configuration->drafing_dir.'/posts');
        if (iterator_count($posts) == 0) {
            return $output->writeln('<info>No posts to generate</info>');
        }
        $output->writeln(sprintf('<info>Generating</info> %s post/s', iterator_count($posts)));
        foreach ($posts as $postFile) {
            $md = file_get_contents($postFile->getRealPath());
            $post = new Post($md);
        }
    }

    private function compileAssets(OutputInterface $output)
    {
        if (count($this->assetProjectsCollection) > 0) {
            $output->writeln('<info>Compiling</info> <comment>static assets</comment>');
            $this->assetProjectsCollection->compile();
            $this->assetProjectsCollection->publish($this->configuration->get('public_dir').'/css');
            $output->writeln('<info>Compiling</info> <comment>Done!</comment>');
        } else {
            $output->writeln('<comment>No assets to compile</comment>');
        }
    }
}
