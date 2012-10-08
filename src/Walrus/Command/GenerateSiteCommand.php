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
    Symfony\Component\Console\Input\ArrayInput,
    Symfony\Component\Finder\Finder,
    Symfony\Component\Filesystem\Filesystem,
    Symfony\Component\Process\Process;
use Spork\ProcessManager,
    Spork\EventDispatcher\EventDispatcher,
    Spork\Fork;

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
        $tmpFolder = sys_get_temp_dir().'/walrus_'.sha1(uniqid());
        $fs = new Filesystem();
        $fs->mkdir($tmpFolder);
        $this->writeRuler($output);
        $this->setup($output, $tmpFolder);
        $this->compileAssets($output, $tmpFolder);
        $this->parsePages($output, $tmpFolder);
        $this->cleanup($output);
        $output->writeln($this->getLine('publishing', 'public folder'));
        $fs->mirror($tmpFolder, $this->container->getParameter('PUBLIC_PATH'));
    }

    private function watch(InputInterface $input, OutputInterface $output)
    {
        while (true) {
            $sha = $this->calculateSha();
            if ($sha !== $this->previosWatch) {
                $output->writeln(date('Y/m/d H:i:s'));
                $this->doExecute($input, $output);
                $this->writeRuler($output);
                $output->writeln('<question>watching for changes...</question>');
                $this->previosWatch = $sha;
            }
            sleep($input->getOption('period'));
        }
    }

    private function calculateSha()
    {
        $iterator = Finder::create()
            ->files()
            ->in(array(
                realpath($this->container->getParameter('DRAFTING_DIR')),
                realpath($this->container->getParameter('THEME_PATH'))
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
        $iterator = Finder::create()->files()->in($this->container->getParameter('PUBLIC_PATH'));
        $fs = new Filesystem();
        $fs->remove($iterator);
    }

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
                    $url = $page->getMetadata()->getUrl().'.html';
                }
                $filename = $dir.'/'.$url;
                if (file_exists($filename)) {
                    unlink($filename);
                }
                $output->writeln($this->getLine('generating page', sprintf('<comment>%s</comment>', $page->getMetadata()->getTitle())));
                $twigMdContent = $this->getTwigMdContent();
                $manager = new ProcessManager(new EventDispatcher());
                $template = $this->getTwigTheme()->loadTemplate('page.html.twig');
                file_put_contents($filename, $template->render(array(
                    'page' => $page,
                    'content' => $twigMdContent->render($page->getContent())
                )));
                /*$manager->fork(function() use($filename, $page, $twigMdContent) {
                    $template = $this->getTwigTheme()->loadTemplate('page.html.twig');
                    file_put_contents($filename, $template->render(array(
                        'page' => $page,
                        'content' => $twigMdContent->render($page->getContent())
                    )));
                })->then(function($out) {
                    printf('Parent %d forked child %d!', posix_getpid(), $out);
                });*/
            }
        } catch (NoPagesCreated $e) {
            $output->writeln('<info>no pages created</info>');
        }
    }

    private function compileAssets(OutputInterface $output, $dir)
    {
        if (count($this->getAssetCollection()) > 0) {
            $output->writeln($this->getLine('compiling', 'static assets (js/css)'));
            $this->getAssetCollection()->compile($output, $dir);
        } else {
            $output->writeln('<comment>No assets to compile</comment>');
        }
    }

    private function publishImages(OutputInterface $output, $dir)
    {
        //$this->container->
    }
}
