<?php
/**
 * User: matteo
 * Date: 14/10/12
 * Time: 0.01
 *
 * Just for fun...
 */

namespace Walrus\Command;

use Walrus\Command\ContainerAwareCommand,
    Walrus\Command\OutputWriterTrait;
use Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Finder\Finder,
    Symfony\Component\Console\Input\InputOption;
use Spork\ProcessManager,
    Spork\EventDispatcher\EventDispatcher;

/**
 * project:watch command
 */
class ProjectWatchCommand extends ContainerAwareCommand
{
    use OutputWriterTrait;

    private $output;

    protected function configure()
    {
        $this
            ->setName('project:watch')
            ->setDescription('Watch project for changes and recompile')
            ->addOption('period', 'p', InputOption::VALUE_REQUIRED, 'seconds between every check', 1);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->writeHeader($output);
        $output->writeln($this->getLine('watching', 'project for changes'));
        $this->writeRuler($output);
        $actualSha = '';
        $pm = new ProcessManager(new EventDispatcher());
        while (true) {
            $sha = $this->calculateSha();
            if ($sha !== $actualSha) {
                $actualSha = $sha;
                try {
                    $this->getTwigTheme()->clearTemplateCache();
                    $this->getTwig()->clearTemplateCache();
                    $pm->fork(function() use($output) {
                        $this->runCommand('generate:site', $output, array('--no-header' => false));
                    });
                } catch (\Exception $e) {
                    $output->writeln('<error>Compilation error!</error>');
                }
                sleep($input->getOption('period'));
            }
        }
    }

    private function calculateSha()
    {
        $draftingPath = $this->container->getParameter('DRAFTING_PATH');
        $themePath = $this->container->getParameter('THEME_PATH');
        $check = array($draftingPath, $themePath);
        $imagesPath = $this->getTheme()->getImages();
        if (null !== $imagesPath) {
            $check[] = $imagesPath;
        }
        $iterator = Finder::create()->files()->in($check);
        $content = '';
        foreach ($iterator as $file) {
            $content .= file_get_contents($file);
        }

        return sha1($content);
    }
}