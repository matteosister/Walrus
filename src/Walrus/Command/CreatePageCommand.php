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
use Symfony\Component\Yaml\Yaml;

/**
 * create:page command
 */
class CreatePageCommand extends ContainerAwareCommand
{
    use OutputWriterTrait;

    /**
     * page folder getter
     *
     * @return string
     */
    private function getPagesFolder()
    {
        return $this->container->getParameter('DRAFTING_PATH').'/pages';
    }

    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->setName('create:page')
            ->setDescription('Create a new page')
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
            $this->runProjectStartup($output, $this->getApplication());
        }
        $title = $input->getArgument('title');
        $date = $this->getUtilities()->getDateFormatted();
        $slug = $this->getUtilities()->getUniqueSlug(array_map(function(Page $p) {
            return $p->getUrl();
        }, $this->container->get('walrus.collection.page')->toArray()), $title);
        $template = $this->getTwig()->loadTemplate('page.md.twig');
        if (0 == $this->getPageCollection()->count()) {
            $parent = '';
            $homepage = true;
            $url = '';
        } else {
            $parent = $input->getArgument('parent') ? $input->getArgument('parent') : $this->getPageCollection()->getHomepage()->getUrl();
            $homepage = false;
            $url =  $slug;
        }
        $options = compact('title', 'date', 'url', 'parent', 'homepage');
        $fileContent = $template->render(array(
            'title' => $title,
            'options' => Yaml::dump($options)
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
