<?php

namespace i2c\GenerateEvaluationBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class MinifyTwigCommand
 *
 * @package i2c\GenerateEvaluationBundle\Command
 */
class MinifyTwigCommand extends Command
{

    /**
     * @inheritdoc
     */
    public function configure()
    {
        $this
            ->setName('i2c:twig:minify')
            ->addOption(
                'twig-folder-path',
                null,
                InputOption::VALUE_REQUIRED,
                'The absolute path of the directory containing the twig files'
            )
            ->setDescription('This command will minify the twig files by removing any extra tabs and EOL');
    }

    /**
     * Execute the command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return null|int null or 0 if everything went fine, or an error code
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $finder = new Finder();

        $twigPath = $input->getOption('twig-folder-path');
        $finder->in($twigPath);

        /** @var SplFileInfo $file */
        foreach ($finder->files() as $file) {
            if ("twig" != $file->getExtension()) {
                continue;
            }

            $twigContent = $file->getContents();
            $twigContent = str_replace("  ", "", $twigContent);
            $twigContent = str_replace("\n", "", $twigContent);
            $twigContent = str_replace("\r\n", "", $twigContent);

            file_put_contents($file->getRealPath(), $twigContent);
        }

        $output->writeln("All twig files were minified");
    }
}
