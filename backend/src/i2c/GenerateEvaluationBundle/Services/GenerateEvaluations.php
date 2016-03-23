<?php

namespace i2c\GenerateEvaluationBundle\Services;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use i2c\EvaluationBundle\Entity\Chapter;
use i2c\EvaluationBundle\Entity\Evaluation;
use JMS\Serializer\Serializer;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Templating\EngineInterface;

/**
 * Class GenerateEvaluations
 *
 * @package i2c\GenerateEvaluationBundle\Services
 */
class GenerateEvaluations
{
    /** @var  ExtractInterface[] */
    protected $extractServicesContainer;

    /** @var  EngineInterface */
    protected $templateRenderer;

    /** @var string */
    protected $templatesPrefix;

    /** @var EntityManager */
    protected $entityManager;

    /** @var  Serializer */
    protected $serializer;

    /** @var  GenerateTableData */
    protected $generateTableDataService;

    /**
     * GenerateEvaluations constructor.
     *
     * @param EngineInterface   $templateRenderer
     * @param Registry          $registry
     * @param Serializer        $serializer
     * @param string            $templatesPrefix
     * @param GenerateTableData $generateTableDataService
     */
    public function __construct(
        EngineInterface $templateRenderer,
        Registry $registry,
        Serializer $serializer,
        $templatesPrefix,
        GenerateTableData $generateTableDataService
    ) {
        $this->templateRenderer = $templateRenderer;

        $this->templatesPrefix = $templatesPrefix;

        $this->entityManager = $registry->getEntityManager();

        $this->serializer = $serializer;

        $this->generateTableDataService = $generateTableDataService;
    }

    /**
     * @param ExtractInterface $extractInterface
     * @param string           $serviceName
     */
    public function addExtractService(ExtractInterface $extractInterface, $serviceName)
    {
        $this->extractServicesContainer[$serviceName] = $extractInterface;
    }

    public function generate($evaluationConfigs, $cids, $versionNumber)
    {
        foreach ($cids as $cid) {
            $evaluationJson = $this->getJsonEntity(
                $cid,
                $evaluationConfigs['twig_name'],
                $evaluationConfigs['data_service'],
                $versionNumber
            );
            /** @var Evaluation $evaluation */
            $evaluation = $this->serializer->deserialize(
                $evaluationJson,
                'i2c\EvaluationBundle\Entity\Evaluation',
                'json'
            );

            /** @var Evaluation $existingEvaluation */
            $existingEvaluation = $this->entityManager->getRepository('EvaluationEvaluationBundle:Evaluation')
                ->findOneBy(['cid' => $cid]);
            if (!is_null($existingEvaluation)) {
                $evaluation = $existingEvaluation;
                $this->removeExistingEvaluationChapters($evaluation);
            }

            $evaluation->setCid($cid);
            $evaluation->markAsGenerating();

            $chapters = [];

            foreach ($evaluationConfigs['chapters'] as $chapterConfig) {
                $tableData = $this->generateTableDataService->generate(
                    $cid,
                    $chapterConfig['table_config'],
                    $versionNumber,
                    $this->templatesPrefix
                );

                $additionalData = [
                    'additional_data' => $chapterConfig['additional_data'],
                    'table_data'      => $tableData,
                ];

                $chapterJson = $this->getJsonEntity(
                    $cid,
                    $chapterConfig['twig_name'],
                    $chapterConfig['data_service'],
                    $versionNumber,
                    $additionalData
                );
                // todo research how to better generate a json string with a twig file so we don't have tabs filled
                // todo lines
                $chapterJson = str_replace("    ", "", $chapterJson);

                /** @var Chapter $chapter */
                $chapter = $this->serializer->deserialize(
                    $chapterJson,
                    'i2c\EvaluationBundle\Entity\Chapter',
                    'json'
                );
                $this->entityManager->persist($chapter);
                $chapters[] = $chapter;
            }

            $businessUnit = $this->entityManager->getRepository('OroOrganizationBundle:BusinessUnit')
                                                ->findOneBy(['id' => $evaluation->getBusinessUnit()->getId()]);
            $evaluation->setOwner($businessUnit);
            $evaluation->setChapters($chapters);
            $this->entityManager->persist($evaluation);
            $this->entityManager->flush();

            $this->updateChaptersLocation($evaluation);

            $evaluation->markAsDraft();
            $this->entityManager->persist($evaluation);

            $this->entityManager->flush();
        }
    }

    public function loadConfigData($jsonPath)
    {
        $fs = new Filesystem();
        if (!$fs->exists($jsonPath)) {
            throw new FileException('No config file found for this version.');
        }

        $jsonContent = file_get_contents($jsonPath);
        if ($jsonContent === false) {
            throw new FileException('There was a problem loading config data.');
        }

        $config = json_decode($jsonContent, true);

        return $config;
    }

    /**
     * @param Evaluation $evaluation
     */
    protected function removeExistingEvaluationChapters(Evaluation $evaluation)
    {
        /** @var Chapter $chapter */
        foreach ($evaluation->getChapters() as $chapter) {
            $conn = $this->entityManager->getConnection();
            $query = sprintf(
                'Delete from evaluation_chapters where chapter_id=\'%s\'',
                $chapter->getId()
            );
            $conn->exec($query);
        }
    }

    /**
     * @param Evaluation $evaluation
     */
    protected function updateChaptersLocation(Evaluation $evaluation)
    {
        $this->entityManager->refresh($evaluation);
        /** @var Chapter $chapter */
        foreach ($evaluation->getChapters() as $chapter) {
            $this->entityManager->refresh($chapter);
            $chapter->setLocation(
                sprintf(
                    '/api/evaluations/%s/chapters/%s',
                    $evaluation->getCid(),
                    $chapter->getId()
                )
            );
            $this->entityManager->persist($chapter);
        }
    }

    /**
     * @param string $serviceName
     * @param string $cid
     *
     * @return array
     */
    protected function getData($serviceName, $cid)
    {
        return $this->extractServicesContainer[$serviceName]->fetchAll($cid);
    }

    /**
     * @param string  $cid
     * @param string  $twigName
     * @param string  $serviceName
     * @param integer $versionNumber
     * @param array   $additionalData
     *
     * @return string
     */
    protected function getJsonEntity($cid, $twigName, $serviceName, $versionNumber, $additionalData = [])
    {
        $data = array_merge($this->getData($serviceName, $cid), $additionalData);

        return $this->templateRenderer->render(
            sprintf('%s:%s:%s', $this->templatesPrefix, $versionNumber, $twigName),
            $data
        );
    }
}
