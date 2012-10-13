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
    Symfony\Component\Finder\Finder;
use Spork\ProcessManager,
    Spork\EventDispatcher\EventDispatcher;

class ProjectWatchCommand extends ContainerAwareCommand
{
    use OutputWriterTrait;

    protected function configure()
    {
        $this
            ->setName('project:watch')
            ->setDescription('Watch project for changes and recompile');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
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
                sleep(1);
            }
        }
    }

    private function calculateSha()
    {
        $draftingPath = $this->container->getParameter('DRAFTING_PATH');
        $themePath = $this->container->getParameter('THEME_PATH');
        $iterator = Finder::create()->files()->in(array($draftingPath, $themePath));
        $content = '';
        foreach ($iterator as $file) {
            $content .= file_get_contents($file);
        }
        return sha1($content);
    }
}