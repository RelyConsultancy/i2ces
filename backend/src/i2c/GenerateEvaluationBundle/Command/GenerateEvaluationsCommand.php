<?php

namespace i2c\GenerateEvaluationBundle\Command;

use i2c\GenerateEvaluationBundle\Services\ExtractCids;
use i2c\GenerateEvaluationBundle\Services\GenerateEvaluations;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * Class GenerateEvaluationsCommand
 *
 * @package i2c\EvaluationBundle\Command
 */
class GenerateEvaluationsCommand extends ContainerAwareCommand
{
    /** @var GenerateEvaluations */
    protected $generateEvaluationsService;
    /** @var  string */
    protected $versionsFolderPath;

    /** @var  ExtractCids */
    protected $extractCidsService;

    /**
     * GenerateEvaluationsCommand constructor.
     *
     * @param GenerateEvaluations $generateEvaluations
     * @param string              $versionsFolderPath
     * @param ExtractCids         $extractCids
     * @param Logger              $logger
     */
    public function __construct(
        GenerateEvaluations $generateEvaluations,
        $versionsFolderPath,
        ExtractCids $extractCids,
        Logger $logger
    ) {
        $this->versionsFolderPath = $versionsFolderPath;
        $this->generateEvaluationsService = $generateEvaluations;
        $this->logger = $logger;
        $this->extractCidsService = $extractCids;

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
             )
             ->addOption(
                 'version-number',
                 null,
                 InputOption::VALUE_REQUIRED,
                 'An integer that represents the version to use to generate the evaluations'
             )
             ->addOption(
                 'update-existing',
                 null,
                 InputOption::VALUE_OPTIONAL,
                 'Boolean representing if an existing evaluation should be generated again, overwriting it',
                 false
             )
             ->addArgument(
                 'cids',
                 InputArgument::IS_ARRAY,
                 'Array of evaluation cids to be used while generating ignoring any other campaigns that are eligible
                  for generation',
                 []
             );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return null|int null or 0 if everything went fine, or an error code
     * @throws \RuntimeException|\LogicException
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $versionNumber = $input->getOption('version-number');

            if (!is_numeric($versionNumber)) {
                throw new \Exception('The "version-number" must be a numeric value');
            }

            $versionNumber = (int) $versionNumber;
            $cids = $this->extractCidsService->getCampaignCidsToBeGenerated(
                $input->getArgument('cids'),
                $input->getOption('update-existing')
            );

            $configFilePath = sprintf(
                '%s/%s/master.json',
                $this->versionsFolderPath,
                $versionNumber
            );
            $configData = $this->generateEvaluationsService->loadConfigData($configFilePath);

            $this->generateEvaluationsService->generate($configData, $cids, $versionNumber);

            $successMessage = 'Evaluations generated successfully!';
            $this->logger->addInfo($successMessage);
            $output->writeln($successMessage);
        } catch (FileException $ex) {
            $this->logger->addCritical($ex->getTraceAsString());
            throw new \RuntimeException($ex->getMessage());
        } catch (\Exception $ex) {
            $this->logger->addCritical($ex->getTraceAsString());
            throw new \LogicException('Something went wrong while generating the evaluations');
        }
    }
}
