<?php

namespace i2c\GeneratePdfBundle\Services;

use Doctrine\ORM\EntityManager;
use i2c\EvaluationBundle\Entity\Evaluation;
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

    /**
     * GenerateEvaluationPdf constructor.
     *
     * @param EntityManager $entityManager
     * @param string        $urlBase
     * @param string        $user
     * @param string        $token
     */
    public function __construct(EntityManager $entityManager, $urlBase, $user, $token)
    {
        $this->entityManager = $entityManager;
        $this->urlBase = $urlBase;
        $this->masterUser = $user;
        $this->masterToken = $token;
    }

    /**
     * @param Evaluation $evaluation
     * @param string     $cookie
     * @param string     $markers
     *
     * @return Evaluation
     */
    public function generatePdf(Evaluation $evaluation, $cookie, $markers)
    {
        $filesystem = new Filesystem();

        $pdfPath = sprintf(
            '%s/%s-temporary.pdf',
            $this->pdfOutputFolder,
            $evaluation->getCid()
        );

        $headers = sprintf('Cookie~ %s`DNT~ 1`x-csrf-token~1', $cookie);

        if (!$filesystem->exists($this->pdfOutputFolder)) {
            $filesystem->mkdir($this->pdfOutputFolder, 0755);
        }

        $this->generateChapterPdf(
            $pdfPath,
            $evaluation->getCid(),
            $headers,
            $markers
        );

        $process = new Process(sprintf('chmod 755 %s', $pdfPath));
        $process->mustRun();

        $evaluation->setTemporaryPdfPath($pdfPath);

        $this->entityManager->persist($evaluation);
        $this->entityManager->flush();

        return $evaluation;
    }

    /**
     * @param string              $pdfPath
     * @param string              $cid
     * @param string              $headers
     * @param string              $markers
     *
     * @return string
     */
    protected function generateChapterPdf($pdfPath, $cid, $headers, $markers)
    {
        //todo add markers to the nodejs command
        $command = sprintf(
            '%s %s/#/preview/%s \'%s\' \'%s\' %s',
            $this->pdfNodeJsCommand,
            $this->urlBase,
            $cid,
            $pdfPath,
            $headers,
            $this->pdfDelay
        );
        $process = new Process($command);
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
