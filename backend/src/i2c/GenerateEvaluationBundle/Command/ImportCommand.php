<?php

namespace i2c\GenerateEvaluationBundle\Command;

use i2c\GenerateEvaluationBundle\Services\ImportAll;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Exception\LogicException;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
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
     * @param array     $importConfig
     * @param Logger    $logger
     * @param ImportAll $importAll
     */
    public function __construct($importConfig, Logger $logger, ImportAll $importAll)
    {
        $this->importConfig = $importConfig;
        $this->logger = $logger;
        $this->importAllService = $importAll;

        parent::__construct("i2c:data:bulk");
    }

    /**
     * @inheritdoc
     */
    public function configure()
    {
        $this
            ->setName('i2c:data:bulk')
            ->setDescription('Imports all new i2c data, and (re)generates corresponding evaluations.');
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

            $finder->in($this->importConfig['folder_path']);
            $finder->directories();
            $finder->sortByName();

            $importCommand = $this->getApplication()->get('i2c:data-import');
            $arguments = [
                '--field-separator' => $this->importConfig['field-separator'],
                '--line-endings'    => $this->importConfig['line-endings'],
            ];

            $latestImportedFolder = null;

            /** @var SplFileInfo $directory */
            foreach ($finder as $directory) {
                if ($directory->getBasename() > $lastImported) {
                    $arguments['--import-folder-path'] = $directory->getRealPath();
                    $importCommand->run(new ArrayInput($arguments), $output);
                    $this->importAllService->endImport($importId, $directory->getBasename());
                    $latestImportedFolder = $directory->getBasename();
                }
            }

            $output->writeln('Import finished');

            $generateCommand = $this->getApplication()->get('i2c:evaluation:generate');
            $arguments = [
                '--version-number' => $this->importConfig['version_number'],
            ];

            $generateCommand->run(new ArrayInput($arguments), $output);

            $output->writeln('Generation finished');
            $this->importAllService->endImport($importId, $latestImportedFolder);

        } catch (\PDOException $ex) {
            $this->logger->addCritical($ex->getTraceAsString());
            $this->importAllService->markImportAsFailure($importId);
            throw new \RuntimeException($ex->getMessage());
        } catch (FileNotFoundException $ex) {
            $this->logger->addCritical($ex->getMessage());
            $this->importAllService->markImportAsFailure($importId);
            throw new LogicException($ex->getMessage());
        } catch (\Exception $ex) {
            $this->logger->addCritical($ex->getTraceAsString());
            $this->importAllService->markImportAsFailure($importId);
            throw new \LogicException('Something went wrong while generating the evaluations');
        }
    }
}
