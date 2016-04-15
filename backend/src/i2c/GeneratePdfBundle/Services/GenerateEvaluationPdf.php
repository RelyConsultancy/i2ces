<?php

namespace i2c\GeneratePdfBundle\Services;

use Doctrine\ORM\EntityManager;
use i2c\EvaluationBundle\Entity\Chapter;
use i2c\EvaluationBundle\Entity\Evaluation;
use i2c\GeneratePdfBundle\Entity\EvaluationPdfConfig;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

/**
 * Class GenerateEvaluationPdf
 *
 * @package i2c\GeneratePdfBundle\Services
 */
class GenerateEvaluationPdf
{
    protected $entityManager;
    protected $urlBase;
    protected $encoder;

    /**
     * GenerateEvaluationPdf constructor.
     *
     * @param EntityManager                $entityManager
     * @param string                       $urlBase
     * @param MessageDigestPasswordEncoder $encoder
     */
    public function __construct(EntityManager $entityManager, $urlBase, MessageDigestPasswordEncoder $encoder)
    {
        $this->entityManager = $entityManager;
        $this->urlBase = $urlBase;
        $this->encoder = $encoder;
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

        /** @var Chapter $chapter */
        foreach ($chapters as $chapter) {
            $headers = $this->getWsseHeader($evaluation->getCid());
            $chapterPdfPath = sprintf(
                '%s/%s.pdf',
                $evaluationVersionPdfDirectory,
                $chapter->getId()
            );
            $command = sprintf(
                '%s %s/#/preview/%s/%s %s \'%s\' 10000',
                $config->getNodeJsCommand(),
                $this->urlBase,
                $evaluation->getCid(),
                $chapter->getId(),
                $chapterPdfPath,
                $headers
            );
            exec($command);

            $chapterPdfs[] = $chapterPdfPath;
        }

        $now = new \DateTime('now');

        $finalPdfPath = sprintf(
            '%s/%s - %s.pdf',
            $evaluationPdfDirectory,
            $now->format('Y-m-d\TH:i:s'),
            $evaluation->getCid()
        );

        $commandThatMergesPdfs = sprintf(
            '%s %s \'%s\'',
            'pdfunite',
            implode(' ', $chapterPdfs),
            $finalPdfPath
        );

        exec($commandThatMergesPdfs);

        $evaluation->setLatestPdfPath($finalPdfPath);

        $this->entityManager->persist($evaluation);
        $this->entityManager->flush();

        foreach ($chapterPdfs as $chapterPdf) {
            $filesystem->remove($chapterPdf);
        }
    }

    protected function getWsseHeader($cid)
    {

        $created = new \DateTime('now');
        $created = $created->format('Y-m-d\TH:i:s');

        // http://stackoverflow.com/questions/18117695/how-to-calculate-wsse-nonce
        $prefix = gethostname();
        $nonce = base64_encode(substr(md5(uniqid($prefix.'_', true)), 0, 16));
        $salt = ''; // do not use real salt here, because API key already encrypted enough

        $user = $this->entityManager->getRepository('OroUserBundle:UserApi')->find(1);
        $passwordDigest = $this->encoder->encodePassword(
            sprintf(
                '%s%s%s',
                base64_decode($nonce),
                $created,
                $user->getApiKey()
            ),
            $salt
        );

        return sprintf(
            'Authorization~ WSSE profile="UsernameToken"`X-WSSE~ UsernameToken Username="%s", PasswordDigest="%s", Nonce="%s", Created="%s"`DNT~ 1',
            $user->getUser()->getUsername(),
            $passwordDigest,
            $nonce,
            $created
        );
    }
}
