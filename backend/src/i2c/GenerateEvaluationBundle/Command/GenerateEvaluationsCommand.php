<?php

namespace i2c\GenerateEvaluationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GenerateEvaluationsCommand
 *
 * @package i2c\EvaluationBundle\Command
 */
class GenerateEvaluationsCommand extends ContainerAwareCommand
{
    protected $generateEvaluationsService;
    protected $versionsFolderPath;

    public function __construct($generateEvaluations, $versionsFolderPath)
    {
        $this->versionsFolderPath = $versionsFolderPath;
        $this->generateEvaluationsService = $generateEvaluations;

        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    public function configure()
    {
        $this->setName('i2c:evaluation:generate')
            ->setDescription(
                'This command will receive a version number and will generate all the evaluations corresponding to the
                configs in the master.json for that version'
            )->addOption(
                'version-number',
                null,
                InputOption::VALUE_REQUIRED,
                'An integer that represents the version to use to generate the evaluations'
            );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return null|int null or 0 if everything went fine, or an error code
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $versionNumber = $input->getOption('version-number');

            if (is_numeric($versionNumber)) {
                $versionNumber = (int) $versionNumber;
            } else {
                $output->writeln('The "version-number" must be a numeric value');

                return -1;
            }

            $configData = $this->generateEvaluationsService->loadConfigData();

            $this->generateEvaluationsService->generate($configData);

            return 0;
        } catch (\Exception $ex) {
            $output->writeln("Something went wrong while generating the evaluations");

            return -2;
        }
        //todo catch load config data errors
        //todo personalize errors for generate evaluations
    }
}
