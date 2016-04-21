<?php

namespace i2c\GeneratePdfBundle\Services;

use Doctrine\ORM\EntityManager;
use i2c\EvaluationBundle\Entity\Chapter;
use i2c\EvaluationBundle\Entity\Evaluation;
use i2c\GeneratePdfBundle\Entity\EvaluationPdfConfig;
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
     * @param Evaluation          $evaluation
     * @param EvaluationPdfConfig $config
     */
    public function generatePdf(Evaluation $evaluation, EvaluationPdfConfig $config)
    {
        if (!$evaluation->isPublished()) {
            return;
        }

        $filesystem = new Filesystem();

        $chapters = $evaluation->getChaptersOrderedByOrder();

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

        $chapterPdfs = [];
        $headers = $this->getHeader();

        $pdfPath = $this->generateChapterPdf(
            'intro',
            $evaluationVersionPdfDirectory,
            $config,
            $evaluation->getCid(),
            $headers
        );

        $chapterPdfs[] = $pdfPath;

        /** @var Chapter $chapter */
        foreach ($chapters as $chapter) {
            $pdfPath = $this->generateChapterPdf(
                $chapter->getId(),
                $evaluationVersionPdfDirectory,
                $config,
                $evaluation->getCid(),
                $headers
            );

            $chapterPdfs[] = $pdfPath;
        }

        $pdfPath = $this->generateChapterPdf(
            'outro',
            $evaluationVersionPdfDirectory,
            $config,
            $evaluation->getCid(),
            $headers
        );

        $chapterPdfs[] = $pdfPath;

        $now = new \DateTime('now');

        $finalPdfPath = sprintf(
            '%s/%s-%s.pdf',
            $evaluationPdfDirectory,
            $evaluation->getCid(),
            $now->format('Y-m-d\TH-i-s')
        );

        $commandThatMergesPdfs = sprintf(
            '%s %s \'%s\'',
            'pdfunite',
            implode(' ', $chapterPdfs),
            $finalPdfPath
        );

        $process = new Process($commandThatMergesPdfs);
        $process->mustRun();

        $process = new Process(sprintf('chmod 755 %s', $finalPdfPath));
        $process->mustRun();

        $evaluation->setLatestPdfPath($finalPdfPath);

        $this->entityManager->persist($evaluation);
        $this->entityManager->flush();

        foreach ($chapterPdfs as $chapterPdf) {
            $filesystem->remove($chapterPdf);
        }
    }

    /**
     * @param string              $chapterId
     * @param string              $pdfLocation
     * @param EvaluationPdfConfig $config
     * @param string              $cid
     * @param string              $headers
     *
     * @return string
     */
    protected function generateChapterPdf($chapterId, $pdfLocation, EvaluationPdfConfig $config, $cid, $headers)
    {
        $now = new \DateTime('now');
        $pdfPath = sprintf(
            '%s/%s-%s.pdf',
            $pdfLocation,
            $chapterId,
            $now->format('Y-m-d\TH-i-s')
        );
        $command = sprintf(
            '%s %s/#/preview/%s/%s %s \'%s\' %s',
            $config->getNodeJsCommand(),
            $this->urlBase,
            $cid,
            $chapterId,
            $pdfPath,
            $headers,
            $config->getDelay()
        );
        $process = new Process($command);
        $process->mustRun();

        return $pdfPath;
    }

    /**
     * @return string
     */
    protected function getHeader()
    {
        $loginRequest = sprintf(
            'curl \'%s/user/login\' -s --compressed -H \'DNT: 1\' -D header.txt | grep _csrf_token',
            $this->urlBase
        );

        $process = new Process($loginRequest);
        $process->mustRun();

        $result = $process->getOutput();

        $initialCookie = $this->getCookieFromResponseHeaderFile('header.txt');

        $start = strpos($result, 'value="') + strlen('value="');
        $end = strpos($result, '"', $start);
        $csrfToken = substr($result, $start, $end - $start);

        $process = new Process(
            sprintf(
                'curl \'%s/user/login-check\' -s --compressed -H \'DNT: 1\' -H \'Cookie: %s\' \
                 --data \'_username=%s&_password=%s&_target_path=&_csrf_token=%s\' \
                 -D header2.txt',
                $this->urlBase,
                $initialCookie,
                $this->masterUser,
                $this->masterToken,
                $csrfToken
            )
        );
        $process->mustRun();

        $cookie = $this->getCookieFromResponseHeaderFile('header2.txt');

        return sprintf(
            'Cookie~ %s`DNT~ 1`x-csrf-token~1',
            $cookie
        );
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    protected function getCookieFromResponseHeaderFile($filename)
    {
        $process = new Process(sprintf('cat %s | grep \'Set-Cookie: BAPID=\'', $filename));
        $process->mustRun();
        $cookieString = $process->getOutput();

        $start = strpos($cookieString, 'BAPID=');
        $end = strpos($cookieString, ';', $start);
        $cookie = substr($cookieString, $start, $end - $start);

        return $cookie;
    }
}
