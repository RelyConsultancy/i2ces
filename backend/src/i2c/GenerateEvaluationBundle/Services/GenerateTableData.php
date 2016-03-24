<?php

namespace i2c\GenerateEvaluationBundle\Services;

use Doctrine\ORM\EntityManager;
use i2c\EvaluationBundle\Entity\TableData;
use JMS\Serializer\Serializer;
use Symfony\Component\Templating\EngineInterface;

/**
 * Class GenerateTableData
 *
 * @package i2c\GenerateEvaluationBundle\Services
 */
class GenerateTableData
{
    /** @var EngineInterface  */
    protected $templateRenderer;

    /** @var EntityManager  */
    protected $entityManager;

    /** @var  ExtractInterface[] */
    protected $extractServicesContainer;

    /**
     * GenerateEvaluations constructor.
     *
     * @param EngineInterface $templateRenderer
     * @param EntityManager        $entityManager
     * @param Serializer      $serializer
     */
    public function __construct($templateRenderer, EntityManager $entityManager, Serializer $serializer)
    {
        $this->templateRenderer = $templateRenderer;

        $this->entityManager = $entityManager;

        $this->serializer = $serializer;
    }

    /**
     * @param ExtractInterface $extractInterface
     * @param string           $serviceName
     */
    public function addExtractService(ExtractInterface $extractInterface, $serviceName)
    {
        $this->extractServicesContainer[$serviceName] = $extractInterface;
    }

    /**
     * Returns an associative array where the keys are the table names and the values are the values are the url to
     * reach the generated data
     *
     * @param string $cid
     * @param array  $tablesConfig
     * @param string $versionNumber
     * @param string $templatePrefix
     *
     * @return array
     */
    public function generate($cid, $tablesConfig, $versionNumber, $templatePrefix)
    {
        $result = [];

        foreach ($tablesConfig as $tableName => $tableConfig) {
            $tableData = $this->getData(
                $tableConfig['data_service'],
                $cid
            );

            $tableJson = $this->getJsonEntity(
                $tableConfig['twig_name'],
                $versionNumber,
                $templatePrefix,
                $tableData
            );

            // todo research how to better generate a json string with a twig file so we don't have tabs filled lines
            $tableJson = str_replace("    ", "", $tableJson);

            /** @var TableData $tableDataEntity */
            $tableDataEntity = new TableData();
            $tableDataEntity->setContent($tableJson);
            $tableDataEntity->setCid($cid);

            $this->entityManager->persist($tableDataEntity);

            $this->entityManager->flush();

            $this->entityManager->refresh($tableDataEntity);

            $result[$tableName] = sprintf(
                '/api/evaluations/%s/dataset/%s',
                $tableDataEntity->getCid(),
                $tableDataEntity->getId()
            );
        }

        return $result;
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
     * @param string  $twigName
     * @param integer $versionNumber
     * @param string  $templatesPrefix
     * @param array   $tableData
     *
     * @return string
     */
    protected function getJsonEntity($twigName, $versionNumber, $templatesPrefix, $tableData = [])
    {
        return $this->templateRenderer->render(
            sprintf('%s:%s/ChartDataSetConfig:%s', $templatesPrefix, $versionNumber, $twigName),
            $tableData
        );
    }
}
