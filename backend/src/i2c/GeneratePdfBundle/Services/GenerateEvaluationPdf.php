<?php

namespace i2c\GeneratePdfBundle\Services;

use Doctrine\ORM\EntityManager;
use i2c\EvaluationBundle\Entity\Chapter;
use i2c\EvaluationBundle\Entity\Evaluation;
use i2c\GeneratePdfBundle\Entity\EvaluationPdfConfig;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class GenerateEvaluationPdf
 *
 * @package i2c\GeneratePdfBundle\Services
 */
class GenerateEvaluationPdf
{
    protected $entityManager;
    protected $urlBase;

    /**
     * GenerateEvaluationPdf constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager, $urlBase)
    {
        $this->entityManager = $entityManager;
        $this->urlBase = $urlBase;
    }

    /**
     * @param Evaluation          $evaluation
     * @param EvaluationPdfConfig $config
     */
    public function generatePdf(Evaluation $evaluation, EvaluationPdfConfig $config)
    {

        $filesystem = new Filesystem();

        $chapters = $evaluation->getChapters();

        $evaluationPdfDirectory = sprintf(
            '%s/%s',
            $config->getOutputDirectory(),
            $evaluation->getCid()
        );

        if (!$filesystem->exists($evaluationPdfDirectory)) {
            $filesystem->mkdir($evaluationPdfDirectory, 0755);
        }

        $evaluationVersionPdfDirectory = sprintf(
            '%s/%s',
            $evaluationPdfDirectory,
            $evaluation->getVersionNumber()
        );

        if (!$filesystem->exists($evaluationVersionPdfDirectory)) {
            $filesystem->mkdir($evaluationVersionPdfDirectory, 0755);
        }


        $commandThatMergesPdfs = "pdfunite ";

        /** @var Chapter $chapter */
        foreach ($chapters as $chapter) {
            $command = sprintf(
                '%s --output=%s/%s.pdf --base-url=%s/#/preview --evaluation=%s
                --chapter=%s',
                $config->getNodeJsCommand(),
                $evaluationVersionPdfDirectory,
                $chapter->getId(),
                $this->urlBase,
                $evaluation->getCid(),
                $chapter->getId()
            );
            exec($command);
            $commandThatMergesPdfs = sprintf(
                '%s %s/%s.pdf',
                $commandThatMergesPdfs,
                $evaluationVersionPdfDirectory,
                $chapter->getId()
            );
        }

        $now = new \DateTime('now');

        $finalPdfPath = sprintf(
            '%s/%s - %s.pdf',
            $evaluationPdfDirectory,
            $now->format('Y-m-d\TH:i:s'),
            $evaluation->getCid()
        );

        $commandThatMergesPdfs = sprintf(
            '%s %s',
            $commandThatMergesPdfs,
            $finalPdfPath
        );

        exec($commandThatMergesPdfs);

        $evaluation->setLatestPdfPath($finalPdfPath);

        $this->entityManager->persist($evaluation);
        $this->entityManager->flush();
    }
}
