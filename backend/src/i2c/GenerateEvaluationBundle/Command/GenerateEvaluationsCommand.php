<?php

namespace i2c\GenerateEvaluationBundle\Command;

use Composer\Downloader\FilesystemException;
use i2c\GenerateEvaluationBundle\Services\GenerateEvaluations;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Exception\LogicException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * Class GenerateEvaluationsCommand
 *
 * @package i2c\EvaluationBundle\Command
 */
class GenerateEvaluationsCommand extends ContainerAwareCommand
{
    /** @var GenerateEvaluations  */
    protected $generateEvaluationsService;
    /** @var  string */
    protected $versionsFolderPath;

    /**
     * GenerateEvaluationsCommand constructor.
     *
     * @param GenerateEvaluations $generateEvaluations
     * @param string              $versionsFolderPath
     * @param Logger              $logger
     */
    public function __construct(GenerateEvaluations $generateEvaluations, $versionsFolderPath, Logger $logger)
    {
        $this->versionsFolderPath = $versionsFolderPath;
        $this->generateEvaluationsService = $generateEvaluations;
        $this->logger = $logger;

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
     * @throws FilesystemException|LogicException
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $versionNumber = $input->getOption('version-number');

            if (!is_numeric($versionNumber)) {
                throw new \Exception('The "version-number" must be a numeric value');
            }

            $versionNumber = (int) $versionNumber;
            $cids = $this->getContainer()
                ->get('i2c_generate_evaluation.extract_cids')
                ->getCampaignCidsToBeGenerated();

            $configFilePath = sprintf(
                '%s/%s/master.json',
                $this->versionsFolderPath,
                $versionNumber
            );
            $configData = $this->generateEvaluationsService->loadConfigData($configFilePath);

            $this->generateEvaluationsService->generate($configData, $cids, $versionNumber);
            $this->logger->addInfo('Evaluations generated successfully!');
        } catch (FileException $ex) {
            $this->logger->addCritical($ex->getMessage());
            throw new FilesystemException($ex->getMessage());
        } catch (\Exception $ex) {
            $this->logger->addCritical($ex->getMessage());
            throw new LogicException($ex->getMessage());
        }
    }
}
