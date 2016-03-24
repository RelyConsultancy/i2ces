<?php

namespace i2c\GenerateEvaluationBundle\Services\Containers;

use i2c\GenerateEvaluationBundle\Services\ExtractInterface;

/**
 * Class ChartDataSetContainer
 *
 * @package i2c\GenerateEvaluationBundle\Services\Containers
 */
class ChartDataSetContainer
{

    /** @var \ArrayObject */
    protected $chartDataSetContainer;

    /**
     * ChartDataSetContainer constructor.
     */
    public function __construct()
    {
        $this->extractContainer = new \ArrayObject();
    }

    /**
     * @param ExtractInterface $extract
     * @param string           $serviceName
     */
    public function addChartDataSetService(ExtractInterface $extract, $serviceName)
    {
        $this->extractContainer[$serviceName] = $extract;
    }

    /**
     * @param $serviceName
     *
     * @return ExtractInterface
     */
    public function getChartDataSetService($serviceName)
    {
        return $this->extractContainer[$serviceName];
    }
}
