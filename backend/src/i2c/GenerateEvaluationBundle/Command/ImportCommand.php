<?php

namespace i2c\GenerateEvaluationBundle\Command;

use i2c\GenerateEvaluationBundle\Services\ImportAll;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class ImportCommand
 *
 * @package i2c\GenerateEvaluationBundle\Command
 */
class ImportCommand extends ContainerAwareCommand
{
    /** @var array */
    protected $importConfig;

    /** @var Logger */
    protected $logger;

    /** @var ImportAll */
    protected $importAllService;

    /**
     * ImportCommand constructor.
     *
     * @param array  $importConfig
     * @param Logger $logger
     */
    public function __construct($importConfig, Logger $logger, ImportAll $importAll)
    {
        $this->importConfig = $importConfig;
        $this->logger = $logger;
        $this->importAllService = $importAll;

        parent::__construct("i2c:import:all");
    }

    /**
     * @inheritdoc
     */
    public function configure()
    {
        $this
            ->setName('i2c:import:all')
            ->setDescription('This command will import all the available i2c data and generate the evaluations.');
    }

    /**
     * Execute the command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return null|int null or 0 if everything went fine, or an error code
     * @throws \RuntimeException
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $lastImported = $this->importAllService->getLastImportDate();

            $importId = $this->importAllService->startImport($this->importConfig);

            $finder = new Finder();

            $finder->directories();
            $finder->sortByName();
            $finder->in($this->importConfig['folder_path']);


            $importCommand = $this->getApplication()->get('i2c:data-import');
            $arguments = [
                '--field-separator' => $this->importConfig['field-separator'],
                '--line-endings' => $this->importConfig['line-endings'],
            ];
            /** @var SplFileInfo $directory */
            foreach ($finder as $directory) {
                if ($directory->getBasename() > $lastImported) {
                    $arguments['--import-folder-path'] = $directory->getRealPath();
                    $importCommand->run(new ArrayInput($arguments), $output);
                    $this->importAllService->endImport($importId, $directory->getBasename());
                }
            }

            $output->writeln('Import finished');

            $generateCommand = $this->getApplication()->get('i2c:evaluation:generate');
            $arguments = [
                '--version-number' => $this->importConfig['version_number'],
            ];

            $generateCommand->run(new ArrayInput($arguments), $output);

            $output->writeln('Generation finished');

        } catch (\PDOException $ex) {
            $this->logger->addCritical($ex->getTraceAsString());
            throw new \RuntimeException($ex->getMessage());
        } finally {
            $this->importAllService->markImportAsFailure($importId);
        }
    }
}
