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
    Walrus\Command\OutputWriterTrait,
    Walrus\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Input\ArrayInput,
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
        $iterator = Finder::create()
            ->files()
            ->in(array(
                realpath($this->getConfiguration()->get('drafting_dir')),
                realpath($this->getConfiguration()->get('theme_dir'))
            ));
        $content = '';
        foreach($iterator as $file) {
            $content .= $file->getContents();
        }
        return sha1($content);
    }

    private function cleanup(OutputInterface $output)
    {
        $output->writeln($this->getLine('cleaning', 'public folder'));
        $iterator = Finder::create()->files()->in($this->getConfiguration()->get('public_dir'));
        $fs = new Filesystem();
        $fs->remove($iterator);
    }

    private function setup(OutputInterface $output)
    {
        $output->writeln($this->getLine('warming up', 'public folder'));
        $cssDir = $this->getConfiguration()->get('public_dir').'/'.AbstractProject::TYPE_CSS;
        $jsDir = $this->getConfiguration()->get('public_dir').'/'.AbstractProject::TYPE_JS;
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
            if (count($this->getPageCollection()) == 0) {
                $output->writeln('<info>No pages to generate</info>');

                return;
            }
            $output->writeln($this->getLine('generating', sprintf('%s page/s', count($this->getPageCollection()))));
            foreach ($this->getPageCollection() as $page) {
                if ($page->getMetadata()->getHomepage()) {
                    $url = 'index.html';
                } else {
                    $url = $page->getMetadata()->getUrl().'.html';
                }
                $filename = $this->getConfiguration()->get('public_dir').'/'.$url;
                if (file_exists($filename)) {
                    unlink($filename);
                }
                $output->writeln($this->getLine('generating page', sprintf('<comment>%s</comment>', $page->getMetadata()->getTitle())));
                file_put_contents($filename, $this->getTwigTheme()->render('page.html.twig', array(
                    'page' => $page,
                    'content' => $this->getTwigString()->render($page->getContent())
                )));
            }
        } catch (\Walrus\Exception\NoPagesCreated $e) {
            $output->writeln('<info>no pages created</info>');
        }
    }

    private function compileAssets(OutputInterface $output)
    {
        if (count($this->getAssetCollection()) > 0) {
            $output->writeln($this->getLine('compiling', 'static assets (js/css)'));
            $this->getAssetCollection()->compile($output, $this->getConfiguration());
        } else {
            $output->writeln('<comment>No assets to compile</comment>');
        }
    }
}
