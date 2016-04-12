<?php

namespace i2c\GenerateEvaluationBundle\Command;

use i2c\GenerateEvaluationBundle\Entity\ImportOption;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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

    /**
     * ImportCommand constructor.
     *
     * @param array  $importConfig
     * @param Logger $logger
     */
    public function __construct($importConfig, Logger $logger)
    {
        $this->rawTablesConfig = $importConfig;
        $this->logger = $logger;

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
            $importOptions = new ImportOption(
                $input->getOption('import-folder-path'),
                $input->getOption('field-separator'),
                $input->getOption('line-endings')
            );

            // use fs
            // foreach do the commands
            /*
            $command = $this->getApplication()->find('demo:greet');
            $arguments = array(
                'command' => 'demo:greet',
                'name'    => 'Fabien',
                '--yell'  => true,
            );

            $greetInput = new ArrayInput($arguments);
            $returnCode = $command->run($greetInput, $output);
            */
            $this->getContainer()
                ->get('i2c_generate_evaluation.import_data')
                ->import($importOptions, $this->rawTablesConfig);

            $successMessage = 'Import finished successfully!';
            $this->logger->addInfo($successMessage);
            $output->writeln($successMessage);
        } catch (\PDOException $ex) {
            $this->logger->addCritical($ex->getTraceAsString());
            throw new \RuntimeException($ex->getMessage());
        }
    }
}
