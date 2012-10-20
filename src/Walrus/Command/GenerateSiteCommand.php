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
    Walrus\Asset\AssetCollection,
    Walrus\Asset\Project\AbstractProject,
    Walrus\Command\OutputWriterTrait,
    Walrus\Command\ContainerAwareCommand,
    Walrus\Exception\NoPagesCreated;
use Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Finder\Finder,
    Symfony\Component\Filesystem\Filesystem;

/**
 * generate:site command
 */
class GenerateSiteCommand extends ContainerAwareCommand
{
    use OutputWriterTrait;

    /**
     * @var string
     */
    protected $previosWatch = null;

    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->setName('generate:site')
            ->setDescription('Generate the website')
            ->addOption('compress-assets', null, InputOption::VALUE_NONE, 'compress the assets')
            ->addOption('group-assets', null, InputOption::VALUE_NONE, 'group the assets')
            ->addOption('no-header', null, InputOption::VALUE_NONE, 'do not display walrus header')
            ->addOption('optimize', 'o', InputOption::VALUE_NONE, 'optimize the output for production');
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
        if (!$input->getOption('no-header')) {
            $this->writeHeader($output);
        }
        $tmpFolder = sys_get_temp_dir().'/walrus_'.sha1(uniqid());
        $fs = new Filesystem();
        $fs->mkdir($tmpFolder);
        $this->writeRuler($output);
        $this->setup($output, $tmpFolder);
        $this->compileAssets($input, $output, $tmpFolder);
        $this->publishImages($output, $tmpFolder);
        $this->parsePages($output, $tmpFolder);
        $this->cleanup($output);
        $output->writeln($this->getLine('publishing', 'public folder'));
        $fs->mirror($tmpFolder, $this->container->getParameter('PUBLIC_PATH'));
    }

    /**
     * cleanup public folder
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    private function cleanup(OutputInterface $output)
    {
        $output->writeln($this->getLine('cleaning', 'public folder'));
        $iterator = Finder::create()->files()->in($this->container->getParameter('PUBLIC_PATH'));
        $fs = new Filesystem();
        $fs->remove($iterator);
        $folders = Finder::create()->directories()->in($this->container->getParameter('PUBLIC_PATH'));
        $fs->remove($folders);
    }

    /**
     * setup
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output OutputInterface
     * @param string                                            $dir    folder
     */
    private function setup(OutputInterface $output, $dir)
    {
        $output->writeln($this->getLine('warming up', 'public folder'));
        $cssDir = $dir.'/'.AbstractProject::TYPE_CSS;
        $jsDir = $dir.'/'.AbstractProject::TYPE_JS;
        $fs = new Filesystem();
        $fs->mkdir($cssDir);
        $fs->mkdir($jsDir);
    }

    /**
     * parse pages
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output output
     * @param string                                            $dir    folder
     *
     * @return void
     */
    private function parsePages(OutputInterface $output, $dir)
    {
        try {
            if (count($this->getPageCollection()) == 0) {
                $output->writeln('<info>No pages to generate</info>');

                return;
            }
            $output->writeln($this->getLine('generating', sprintf('%s page/s', count($this->getPageCollection()))));
            foreach ($this->getPageCollection() as $page) {
                if ($page->getMetadata()->getHomepage()) {
                    $url = 'index.html';
                } else {
                    $url = $page->getUrl();
                }
                $filename = $dir.'/'.$url;
                $pathinfo = pathinfo($filename);
                $fs = new Filesystem();
                $fs->mkdir($pathinfo['dirname']);
                if (file_exists($filename)) {
                    unlink($filename);
                }
                $output->writeln($this->getLine('generating page', sprintf('<comment>%s</comment>', $page->getMetadata()->getTitle())));
                $twigMdContent = $this->getTwigMdContent();
                $template = $this->getTwigTheme()->loadTemplate('page.html.twig');
                file_put_contents($filename, $template->render(array(
                    'page' => $page,
                    'content' => $twigMdContent->render($page->getContent())
                )));
            }
        } catch (NoPagesCreated $e) {
            $output->writeln('<info>no pages created</info>');
        }
    }

    /**
     * compile assets
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input  input
     * @param \Symfony\Component\Console\Output\OutputInterface $output output
     * @param string                                            $dir    folder
     *
     * @return void
     */
    private function compileAssets(InputInterface $input, OutputInterface $output, $dir)
    {
        if ($input->getOption('group-assets') || $input->getOption('optimize')) {
            $this->getAssetCollection()->setGroupAssets(true);
        }
        if ($input->getOption('compress-assets') || $input->getOption('optimize')) {
            $this->getAssetCollection()->setForceAssetCompression(true);
        }
        if (count($this->getAssetCollection()) > 0) {
            $output->writeln($this->getLine('compiling', 'static assets (js/css)'));
            $this->getAssetCollection()->compile($output, $dir);
        } else {
            $output->writeln('<comment>No assets to compile</comment>');
        }
    }

    /**
     * public images folder
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output output
     * @param string                                            $dir    folder
     */
    private function publishImages(OutputInterface $output, $dir)
    {
        if (null === $this->getTheme()->getImages()) {
            return;
        }
        $images = $this->getTheme()->getImages();
        $iterator = Finder::create()->files()->in($images);
        $fs = new Filesystem();
        $output->writeln($this->getLine('mirroring', 'images'));
        $fs->mirror($images, $dir, $iterator);
    }
}
