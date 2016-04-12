<?php

namespace i2c\GenerateEvaluationBundle\Command;

use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use i2c\GenerateEvaluationBundle\Entity\ImportOption;
use Symfony\Component\DependencyInjection\Exception\LogicException;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class RawImportCommand
 *
 * @package i2c\EvaluationBundle\Command
 */
class RawImportCommand extends ContainerAwareCommand
{

    /** @var array */
    protected $rawTablesConfig;

    /** @var string */
    protected $importValidationFile;

    /** @var Logger */
    protected $logger;

    /**
     * RawImportCommand constructor.
     *
     * @param array  $rawTablesConfig
     * @param string $importValidationFile
     * @param Logger $logger
     */
    public function __construct($rawTablesConfig, $importValidationFile, Logger $logger)
    {
        $this->rawTablesConfig = $rawTablesConfig;
        $this->importValidationFile = $importValidationFile;
        $this->logger = $logger;

        parent::__construct("i2c:data-import");
    }
    /**
     * @inheritdoc
     */
    public function configure()
    {
        $this
            ->setName('i2c:data-import')
            ->addOption(
                'import-folder-path',
                null,
                InputOption::VALUE_REQUIRED,
                'The absolute path of the directory containing the csv files'
            )
            ->addOption(
                'field-separator',
                null,
                InputOption::VALUE_OPTIONAL,
                'The character(s) that separate the fields in the csv file',
                ','
            )
            ->addOption(
                'line-endings',
                null,
                InputOption::VALUE_OPTIONAL,
                'The characters that separate lines in the csv file, defaults to "\n"',
                '\n'
            )
            ->setDescription('This command will import the i2c data from a csv to a table');
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
            $importOptions = new ImportOption(
                $input->getOption('import-folder-path'),
                $input->getOption('field-separator'),
                $input->getOption('line-endings')
            );

            $fs = new Filesystem();
            $importValidationFilePath = sprintf(
                '%s/%s',
                $importOptions->getImportFilePath(),
                $this->importValidationFile
            );
            if (!$fs->exists($importValidationFilePath)) {
                throw new FileNotFoundException(
                    sprintf('The import for path \'%s\' is not finished!', $importOptions->getImportFilePath())
                );
            }

            $this->getContainer()
                ->get('i2c_generate_evaluation.import_data')
                ->import($importOptions, $this->rawTablesConfig);

            $successMessage = 'Import finished successfully!';
            $this->logger->addInfo($successMessage);
            $output->writeln($successMessage);
        } catch (\PDOException $ex) {
            $this->logger->addCritical($ex->getTraceAsString());
            throw new \RuntimeException($ex->getMessage());
        } catch (FileNotFoundException $ex) {
            $this->logger->addCritical($ex->getMessage());
            throw new LogicException($ex->getMessage());
        }
    }
}
