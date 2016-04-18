<?php

namespace i2c\GeneratePdfBundle\Command;

use i2c\EvaluationBundle\Entity\Evaluation;
use i2c\EvaluationBundle\Repository\EvaluationRepository;
use i2c\GeneratePdfBundle\Entity\EvaluationPdfConfig;
use i2c\GeneratePdfBundle\Services\EvaluationQueue;
use i2c\GeneratePdfBundle\Services\GenerateEvaluationPdf;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GeneratePDF
 *
 * @package i2c\GeneratePdfBundle\Command
 */
class GeneratePDF extends ContainerAwareCommand
{
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
        $cids = $this->getEvaluationQueueService()->getEvaluationForGeneration();

        if (empty($cids)) {
            $output->writeln('No pdfs to generate');
            return;
        }

        $config = new EvaluationPdfConfig();

        $config->setNodeJsCommand($input->getOption('node-command'));
        $config->setOutputDirectory($input->getOption('output-folder'));
        $config->setDelay($input->getOption('delay'));


        foreach ($cids as $cid) {
            /** @var Evaluation $evaluation */
            $evaluation = $this->getEvaluationRepository()->findOneBy(['cid' => $cid]);

            if (is_null($evaluation)) {
                continue;
            }

            $this->getGenerateEvaluationPdfService()->generatePdf($evaluation, $config);

            $this->getEvaluationQueueService()->removeFromQueue($cid);
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
