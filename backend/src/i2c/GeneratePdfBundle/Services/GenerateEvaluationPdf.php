<?php

namespace i2c\GeneratePdfBundle\Services;

use Doctrine\ORM\EntityManager;
use i2c\EvaluationBundle\Entity\Evaluation;
use Monolog\Logger;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

/**
 * Class GenerateEvaluationPdf
 *
 * @package i2c\GeneratePdfBundle\Services
 */
class GenerateEvaluationPdf
{
    protected $entityManager;
    protected $urlBase;
    protected $masterUser;
    protected $masterToken;
    protected $pdfDelay;
    protected $pdfNodeJsCommand;
    protected $pdfOutputFolder;
    protected $logger;

    /**
     * GenerateEvaluationPdf constructor.
     *
     * @param EntityManager $entityManager
     * @param string        $urlBase
     * @param string        $user
     * @param string        $token
     */
    public function __construct(EntityManager $entityManager, $urlBase, $user, $token, Logger $logger)
    {
        $this->entityManager = $entityManager;
        $this->urlBase = $urlBase;
        $this->masterUser = $user;
        $this->masterToken = $token;
        $this->logger = $logger;
    }

    /**
     * @param Evaluation $evaluation
     * @param string     $cookie
     *
     * @return Evaluation
     */
    public function generatePdf(Evaluation $evaluation, $cookie)
    {
        $filesystem = new Filesystem();

        $pdfPath = sprintf(
            '%s/%s-temporary.pdf',
            $this->pdfOutputFolder,
            $evaluation->getCid()
        );

        $headers = sprintf('Cookie~%s`DNT~1`x-csrf-token~1', $cookie);

        if (!$filesystem->exists($this->pdfOutputFolder)) {
            $filesystem->mkdir($this->pdfOutputFolder, 0755);
        }

        $this->generatePdfForEvaluation(
            $pdfPath,
            $evaluation->getCid(),
            $headers
        );

        $evaluation->setTemporaryPdfPath($pdfPath);

        $this->entityManager->persist($evaluation);
        $this->entityManager->flush();

        return $evaluation;
    }

    /**
     * @param string              $pdfPath
     * @param string              $cid
     * @param string              $headers
     *
     * @return string
     */
    protected function generatePdfForEvaluation($pdfPath, $cid, $headers)
    {
        $filesystem = new Filesystem();
        $command = sprintf(
            'exec %s %s/#/preview/%s \'%s\' \'%s\' %s &',
            $this->pdfNodeJsCommand,
            $this->urlBase,
            $cid,
            $pdfPath,
            $headers,
            $this->pdfDelay
        );
        if ($filesystem->exists($pdfPath)) {
            $filesystem->remove($pdfPath);
        }

        $this->logger->addDebug(sprintf('Running the pdf generation command %s', $command));

        $theTimeout = 60;
        $process = new Process($command);
        $process->setTimeout($theTimeout);
        $process->setIdleTimeout($theTimeout);
        $process->start();

        return $pdfPath;
    }

    /**
     * @param mixed $pdfDelay
     */
    public function setPdfDelay($pdfDelay)
    {
        $this->pdfDelay = $pdfDelay;
    }

    /**
     * @param mixed $pdfNodeJsCommand
     */
    public function setPdfNodeJsCommand($pdfNodeJsCommand)
    {
        $this->pdfNodeJsCommand = $pdfNodeJsCommand;
    }

    /**
     * @param mixed $pdfOutputFolder
     */
    public function setPdfOutputFolder($pdfOutputFolder)
    {
        $this->pdfOutputFolder = $pdfOutputFolder;
    }
}
