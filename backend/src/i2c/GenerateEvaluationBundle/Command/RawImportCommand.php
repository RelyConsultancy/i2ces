<?php

namespace i2c\GenerateEvaluationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use i2c\GenerateEvaluationBundle\Entity\ImportOption;

/**
 * Class RawImportCommand
 *
 * @package i2c\EvaluationBundle\Command
 */
class RawImportCommand extends ContainerAwareCommand
{

    /** @var array */
    protected $rawTablesConfig;

    /**
     * RawImportCommand constructor.
     *
     * @param array $rawTablesConfig
     */
    public function __construct($rawTablesConfig)
    {
        $this->rawTablesConfig = $rawTablesConfig;

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
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $importOptions = new ImportOption(
                $input->getOption('import-folder-path'),
                $input->getOption('field-separator'),
                $input->getOption('line-endings')
            );

            $this->getContainer()
                ->get('i2c_generate_evaluation.import_date')
                ->import($importOptions, $this->rawTablesConfig);

            $output->writeln('Import finished successfully!');
        } catch (\PDOException $ex) {
            $output->writeln($ex->getMessage());
        }
    }
}
