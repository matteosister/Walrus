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
    Symfony\Component\DependencyInjection\ContainerInterface;
use Walrus\Utilities\Utilities,
    Walrus\DI\Configuration,
    Walrus\MDObject\Page\Page,
    Walrus\Command\ContainerAwareCommand;

/**
 * create:page command
 */
class CreatePageCommand extends ContainerAwareCommand
{
    use OutputWriterTrait;

    /**
     * @var \Twig_Environment
     */
    //protected $twigEnvironment;

    /**
     * @var \Walrus\Utilities\Utilities
     */
    //protected $utilities;

    /**
     * @var string
     */
    //private $pagesFolder;

    /**
     * @var
     */
    private $pageCollection;

    /**
     * page folder getter
     *
     * @return string
     */
    private function getPagesFolder()
    {
        return $this->getConfiguration()->get('drafting_dir').'/pages';
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
        if (!is_dir($this->getPagesFolder())) {
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
        $date = $this->getUtilities()->getDateFormatted();
        $title = $input->getArgument('title');
        $slug = $this->getUtilities()->getUniqueSlug(array_map(function(Page $p) {
            return $p->getMetadata()->getUrl();
        }, $this->container->get('walrus.collection.page')->toArray()), $title);
        $template = $this->getTwig()->loadTemplate('page.md.twig');
        $homepage = $input->getOption('homepage') ? true : false;
        $fileContent = $template->render(array(
            'title' => $title,
            'date' => $date,
            'url' => $input->getOption('homepage') ? '' : $slug,
            'homepage' => 0 === $this->getPageCollection()->count() ? true : $homepage
        ));
        $dir = $this->getPagesFolder();
        if (!is_dir($dir)) {
            mkdir($dir);
        }
        $fileName = $dir.'/'.$date.'_page_'.$slug.'.md';
        file_put_contents($fileName, $fileContent);
        $this->writeRuler($output);
        $output->writeln(sprintf('<info>Page</info> <comment>%s</comment> created', $title));
    }
}
