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
     * @param string        $urlBase
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

        $chapterPdfs = [];
        $headers = $this->getHeader();

        /** @var Chapter $chapter */
        foreach ($chapters as $chapter) {
            $chapterPdfPath = sprintf(
                '%s/%s.pdf',
                $evaluationVersionPdfDirectory,
                $chapter->getId()
            );
            $command = sprintf(
                '%s %s/#/preview/%s/%s %s \'%s\' %s',
                $config->getNodeJsCommand(),
                $this->urlBase,
                $evaluation->getCid(),
                $chapter->getId(),
                $chapterPdfPath,
                $headers,
                $config->getDelay()
            );
            exec($command, $output);

            $chapterPdfs[] = $chapterPdfPath;
        }

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

        exec($commandThatMergesPdfs);

        exec(sprintf('chmod 755 %s', $finalPdfPath));

        $evaluation->setLatestPdfPath($finalPdfPath);

        $this->entityManager->persist($evaluation);
        $this->entityManager->flush();

        foreach ($chapterPdfs as $chapterPdf) {
            $filesystem->remove($chapterPdf);
        }
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
        $result = exec($loginRequest);

        $initialCookie = $this->getCookieFromResponseHeaderFile('header.txt');

        $start = strpos($result, 'value="') + strlen('value="');
        $end = strpos($result, '"', $start);
        $csrfToken = substr($result, $start, $end - $start);

        exec(
            sprintf(
                'curl \'%s/user/login-check\' -s --compressed -H \'DNT: 1\' -H \'Cookie: %s\' \
                 --data \'_username=%s&_password=%s&_target_path=&_csrf_token=%s\' \
                 -D header2.txt',
                $this->urlBase,
                $initialCookie,
                'user',
                'pass',
                $csrfToken
            )
        );

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
        $cookieString = exec(sprintf('cat %s | grep \'Set-Cookie: BAPID=\'', $filename));

        $start = strpos($cookieString, 'BAPID=');
        $end = strpos($cookieString, ';', $start);
        $cookie = substr($cookieString, $start, $end - $start);

        return $cookie;
    }
}
