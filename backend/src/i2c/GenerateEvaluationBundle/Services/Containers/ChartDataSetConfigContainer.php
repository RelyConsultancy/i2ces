<?php

namespace i2c\GenerateEvaluationBundle\Services\Containers;

use i2c\GenerateEvaluationBundle\Services\ChartDataSetConfigInterface;
use i2c\GenerateEvaluationBundle\Services\ExtractInterface;

/**
 * Class ChartDataSetConfigContainer
 *
 * @package i2c\GenerateEvaluationBundle\Services\Containers
 */
class ChartDataSetConfigContainer
{
    /** @var \ArrayObject */
    protected $chartDataSetConfigContainer;

    /**
     * ExtractContainer constructor.
     */
    public function __construct()
    {
        $this->chartDataSetConfigContainer = new \ArrayObject();
    }

    /**
     * @param ChartDataSetConfigInterface $chartDataSetConfigInterface
     * @param string                      $serviceName
     */
    public function addChartDataSetConfigService(ChartDataSetConfigInterface $chartDataSetConfigInterface, $serviceName)
    {
        $this->chartDataSetConfigContainer[$serviceName] = $chartDataSetConfigInterface;
    }

    /**
     * @param $serviceName
     *
     * @return ChartDataSetConfigInterface
     */
    public function getChartDataSetConfigService($serviceName)
    {
        return $this->chartDataSetConfigContainer[$serviceName];
    }
}
