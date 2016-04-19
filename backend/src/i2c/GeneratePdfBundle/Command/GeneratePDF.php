<?php

namespace i2c\GeneratePdfBundle\Command;

use i2c\EvaluationBundle\Entity\Evaluation;
use i2c\EvaluationBundle\Repository\EvaluationRepository;
use i2c\GeneratePdfBundle\Entity\EvaluationPdfConfig;
use i2c\GeneratePdfBundle\Services\EvaluationQueue;
use i2c\GeneratePdfBundle\Services\GenerateEvaluationPdf;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * Class GeneratePDF
 *
 * @package i2c\GeneratePdfBundle\Command
 */
class GeneratePDF extends ContainerAwareCommand
{
    /** @var Logger */
    protected $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
        parent::__construct('i2c:generate-pdf:evaluation');
    }

    public function configure()
    {
        $this->setName('i2c:generate-pdf:evaluation')
             ->addOption(
                 'output-folder',
                 null,
                 InputOption::VALUE_REQUIRED,
                 'The folder in which the pdfs will be generated'
             )
             ->addOption(
                 'node-command',
                 null,
                 InputOption::VALUE_REQUIRED,
                 'The absolute path to the node command'
             )
             ->addOption(
                 'delay',
                 null,
                 InputOption::VALUE_REQUIRED,
                 'The delay for the rendering in order for all the data to load'
             )
             ->setDescription(
                 'Generates a PDF file for an evaluation and stores the resulting location in the database'
             );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $cidsArray = $this->getEvaluationQueueService()->getEvaluationForGeneration();

            if (empty($cidsArray)) {
                $output->writeln('No pdfs to generate');

                return;
            }

            $config = new EvaluationPdfConfig();

            $config->setNodeJsCommand($input->getOption('node-command'));
            $config->setOutputDirectory($input->getOption('output-folder'));
            $config->setDelay($input->getOption('delay'));

            foreach ($cidsArray as $item) {
                /** @var Evaluation $evaluation */
                $evaluation = $this->getEvaluationRepository()->findOneBy(['cid' => $item['cid']]);

                if (is_null($evaluation)) {
                    continue;
                }

                $this->getGenerateEvaluationPdfService()->generatePdf($evaluation, $config);

                $this->getEvaluationQueueService()->removeFromQueue($item['cid']);
            }
        } catch (ProcessFailedException $ex) {
            $this->logger->addCritical($ex->getMessage());
            $this->logger->addCritical($ex->getTraceAsString());
            throw new \RuntimeException($ex->getMessage());
        }
    }

    /**
     * @return GenerateEvaluationPdf
     */
    protected function getGenerateEvaluationPdfService()
    {
        return $this->getContainer()->get('i2c_generate_pdf.generate_evaluation_pdf_service');
    }

    /**
     * @return EvaluationQueue
     */
    protected function getEvaluationQueueService()
    {
        return $this->getContainer()->get('i2c_generate_pdf.evaluation_queue_service');
    }

    /**
     * @return EvaluationRepository
     */
    protected function getEvaluationRepository()
    {
        return $this->getContainer()->get('doctrine.orm.default_entity_manager')->getRepository(
            'i2cEvaluationBundle:Evaluation'
        );
    }
}
