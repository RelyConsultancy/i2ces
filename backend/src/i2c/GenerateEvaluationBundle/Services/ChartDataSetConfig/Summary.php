<?php

namespace i2c\GenerateEvaluationBundle\Services\ChartDataSetConfig;

use i2c\GenerateEvaluationBundle\Services\ChartDataSetConfigInterface;

/**
 * Class Summary
 *
 * @package i2c\GenerateEvaluationBundle\Services\ChartDataSetConfig
 */
class Summary implements ChartDataSetConfigInterface
{

    /**
     * Returns an array with table config data.
     *
     * @param string $cid
     *
     * @return array
     */
    public function fetchChartDataSetConfig($cid)
    {
        return [];
    }
}
