<?php

namespace i2c\GenerateEvaluationBundle\Services\ChartDataSetConfig;

use i2c\GenerateEvaluationBundle\Services\ChartDataSetConfigInterface;

/**
 * Class CampaignBackground
 *
 * @package i2c\GenerateEvaluationBundle\Services\ChartDataSetConfig
 */
class CampaignBackground implements ChartDataSetConfigInterface
{

    /**
     * Returns an array with table config data.
     *
     * @param string $cid
     *
     * @return array
     */
    public function getTableConfig($cid)
    {
        return [];
    }
}
