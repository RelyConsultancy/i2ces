<?php

namespace i2c\GenerateEvaluationBundle\Services;

use Doctrine\ORM\EntityManager;
use i2c\EvaluationBundle\Entity\Chapter;
use i2c\EvaluationBundle\Entity\Evaluation;
use i2c\GenerateEvaluationBundle\Services\Containers\ChartDataSetConfigContainer;
use i2c\GenerateEvaluationBundle\Services\Containers\ExtractContainer;
use JMS\Serializer\Serializer;
use Oro\Bundle\OrganizationBundle\Entity\BusinessUnit;
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
    /** @var  ExtractContainer */
    protected $extractServicesContainer;

    /** @var  EngineInterface */
    protected $templateRenderer;

    /** @var string */
    protected $templatesPrefix;

    /** @var EntityManager */
    protected $entityManager;

    /** @var  Serializer */
    protected $serializer;

    /** @var  GenerateChartDataSet */
    protected $generateChartDataSetService;

    /** @var ChartDataSetConfigContainer */
    protected $chartDataSetConfigContainer;

    /** @var OptionalChaptersConfig */
    protected $optionalChaptersConfigService;

    /** @var  string */
    protected $masterUser;

    /**
     * GenerateEvaluations constructor.
     *
     * @param EngineInterface      $templateRenderer
     * @param EntityManager        $entityManager
     * @param Serializer           $serializer
     * @param string               $templatesPrefix
     * @param GenerateChartDataSet $generateTableDataService
     */
    public function __construct(
        EngineInterface $templateRenderer,
        EntityManager $entityManager,
        Serializer $serializer,
        $templatesPrefix,
        GenerateChartDataSet $generateTableDataService
    ) {
        $this->templateRenderer = $templateRenderer;

        $this->templatesPrefix = $templatesPrefix;

        $this->entityManager = $entityManager;

        $this->serializer = $serializer;

        $this->generateChartDataSetService = $generateTableDataService;
    }

    /**
     * @param string $username
     */
    public function setMasterUser($username)
    {
        $this->masterUser = $username;
    }

    /**
     * @param ExtractContainer $extractContainer
     */
    public function setExtractContainer(ExtractContainer $extractContainer)
    {
        $this->extractServicesContainer = $extractContainer;
    }

    /**
     * @param ChartDataSetConfigContainer $chartDataSetConfigContainer
     */
    public function setChartDataSetConfigContainer(ChartDataSetConfigContainer $chartDataSetConfigContainer)
    {
        $this->chartDataSetConfigContainer = $chartDataSetConfigContainer;
    }

    /**
     * @param OptionalChaptersConfig $optionalChaptersConfig
     */
    public function setOptionalChaptersConfigService(OptionalChaptersConfig $optionalChaptersConfig)
    {
        $this->optionalChaptersConfigService = $optionalChaptersConfig;
    }

    /**
     * @param array  $evaluationConfigs
     * @param array  $cids
     * @param string $versionNumber
     */
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

            $evaluation = $this->updateExistingIfPresent($evaluation, $cid);

            $evaluation->setVersionNumber($versionNumber);
            $evaluation->setLatestPdfPath(null);
            $evaluation->markAsGenerating();

            $chapters = [];

            $chaptersConfig = array_merge(
                $evaluationConfigs['chapters'],
                $this->optionalChaptersConfigService->fetchOptionalChaptersConfig($cid)
            );

            foreach ($chaptersConfig as $chapterConfig) {
                $chartDataSetSources = $this->generateChartDataSetService->generate(
                    $cid,
                    $this->getChartDataSetConfig($chapterConfig['chart_data_set_service_name'], $cid),
                    $versionNumber,
                    $this->templatesPrefix
                );

                $additionalData = [
                    'additional_data' => $chapterConfig['additional_data'],
                    'chart_data_set'  => $chartDataSetSources,
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

            $evaluation->setOwner($this->getBusinessUnit($cid));

            $evaluation->setChapters($chapters);
            $this->entityManager->persist($evaluation);
            $this->entityManager->flush();

            $this->updateChaptersLocation($evaluation);

            $evaluation->markAsDraft();
            $this->entityManager->persist($evaluation);

            $this->entityManager->flush();
        }
    }

    /**
     * @param string $jsonPath
     *
     * @return mixed
     */
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
     * @param string     $cid
     *
     * @return Evaluation
     */
    protected function updateExistingIfPresent($evaluation, $cid)
    {
        /** @var Evaluation $existingEvaluation */
        $existingEvaluation = $this->entityManager->getRepository('i2cEvaluationBundle:Evaluation')
                                                  ->findOneBy(['cid' => $cid]);
        if (!is_null($existingEvaluation)) {
            $evaluation->setId($existingEvaluation->getId());
            /** @var Evaluation $evaluation */
            $evaluation = $this->entityManager->merge($evaluation);
            $this->removeExistingEvaluationChapters($existingEvaluation);
        }

        $evaluation->setCid($cid);

        return $evaluation;
    }

    /**
     * @param string $cid
     *
     * @return BusinessUnit
     */
    protected function getBusinessUnit($cid)
    {
        $businessUnitName = $this->getBusinessUnitName($cid);
        $user = $this->entityManager->getRepository('OroUserBundle:User')->findOneBy(
            ['username' => $this->masterUser]
        );

        $businessUnit = $this->entityManager->getRepository('OroOrganizationBundle:BusinessUnit')
                                            ->findOneBy(['name' => $businessUnitName]);
        if (is_null($businessUnit)) {
            $businessUnit = $this->getNewBusinessUnit($businessUnitName);
        }

        $user->addBusinessUnit($businessUnit);
        $this->entityManager->persist($user);

        return $businessUnit;
    }

    /**
     * @param string $name
     *
     * @return BusinessUnit
     */
    protected function getNewBusinessUnit($name)
    {
        $mainBusinessUnit = $this->entityManager->getRepository('OroOrganizationBundle:BusinessUnit')->getFirst();

        $businessUnit = new BusinessUnit();
        $businessUnit->setName($name);
        $businessUnit->setOrganization($mainBusinessUnit->getOrganization());
        $businessUnit->setOwner($mainBusinessUnit);

        $this->entityManager->persist($businessUnit);

        return $businessUnit;
    }

    /**
     * @param string $cid
     *
     * @return mixed
     */
    protected function getBusinessUnitName($cid)
    {
        $query = sprintf(
            'SELECT supplier FROM ie_campaign_data WHERE master_campaign_id=\'%s\'',
            $cid
        );

        return $this->entityManager->getConnection()->fetchColumn($query);
    }

    /**
     * @param Evaluation $evaluation
     */
    protected function removeExistingEvaluationChapters(Evaluation $evaluation)
    {
        $conn = $this->entityManager->getConnection();
        $chapters = $evaluation->getChaptersIncludingAdditionalData();
        /** @var Chapter $chapter */
        foreach ($chapters as $chapter) {
            $query = sprintf(
                'DELETE FROM i2c_evaluation_chapters WHERE chapter_id=\'%s\'',
                $chapter->getId()
            );
            $conn->exec($query);

            $query = sprintf(
                'DELETE FROM i2c_chapter WHERE id=\'%s\'',
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
        return $this->extractServicesContainer->getExtractService($serviceName)->fetchAll($cid);
    }

    /**
     * @param string $serviceName
     * @param string $cid
     *
     * @return array
     */
    protected function getChartDataSetConfig($serviceName, $cid)
    {
        return $this->chartDataSetConfigContainer->getChartDataSetConfigService($serviceName)->fetchChartDataSetConfig(
            $cid
        );
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
