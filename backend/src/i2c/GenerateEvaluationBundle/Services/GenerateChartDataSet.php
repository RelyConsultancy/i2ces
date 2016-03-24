<?php

namespace i2c\GenerateEvaluationBundle\Services;

use Doctrine\ORM\EntityManager;
use i2c\EvaluationBundle\Entity\ChartDataSet;
use i2c\GenerateEvaluationBundle\Services\Containers\ChartDataSetContainer;
use JMS\Serializer\Serializer;
use Symfony\Component\Templating\EngineInterface;

/**
 * Class GenerateTableData
 *
 * @package i2c\GenerateEvaluationBundle\Services
 */
class GenerateChartDataSet
{
    /** @var EngineInterface  */
    protected $templateRenderer;

    /** @var EntityManager  */
    protected $entityManager;

    /** @var  ChartDataSetContainer */
    protected $chartDataSetContainer;

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
     * @param ChartDataSetContainer $chartDataSetContainer
     */
    public function setChartDataSetContainer(ChartDataSetContainer $chartDataSetContainer)
    {
        $this->chartDataSetContainer = $chartDataSetContainer;
    }

    /**
     * Returns an associative array where the keys are the table names and the values are the values are the url to
     * reach the generated data
     *
     * @param string $cid
     * @param array  $chartDataSetConfig
     * @param string $versionNumber
     * @param string $templatePrefix
     *
     * @return array
     */
    public function generate($cid, $chartDataSetConfig, $versionNumber, $templatePrefix)
    {
        $result = [];

        foreach ($chartDataSetConfig as $chartName => $chartConfig) {
            $chartDataSetData = $this->getData(
                $chartConfig['data_service'],
                $cid
            );

            $chartDataSetJson = $this->getJsonEntity(
                $chartConfig['twig_name'],
                $versionNumber,
                $templatePrefix,
                $chartDataSetData
            );

            // todo research how to better generate a json string with a twig file so we don't have tabs filled lines
            $chartDataSetJson = str_replace("    ", "", $chartDataSetJson);

            /** @var ChartDataSet $chartDataSetEntity */
            $chartDataSetEntity = new ChartDataSet();
            $chartDataSetEntity->setContent($chartDataSetJson);
            $chartDataSetEntity->setCid($cid);

            $this->entityManager->persist($chartDataSetEntity);

            $this->entityManager->flush();

            $this->entityManager->refresh($chartDataSetEntity);

            $result[$chartName] = sprintf(
                '/api/evaluations/%s/dataset/%s',
                $chartDataSetEntity->getCid(),
                $chartDataSetEntity->getId()
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
        return $this->chartDataSetContainer->getChartDataSetService($serviceName)->fetchAll($cid);
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
            sprintf('%s:%s/ChartDataSet:%s', $templatesPrefix, $versionNumber, $twigName),
            $tableData
        );
    }
}
