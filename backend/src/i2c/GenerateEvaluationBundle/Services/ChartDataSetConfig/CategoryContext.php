<?php

namespace i2c\GenerateEvaluationBundle\Services\ChartDataSetConfig;

use i2c\GenerateEvaluationBundle\Services\ChartDataSetConfigInterface;

/**
 * Class CategoryContext
 *
 * @package i2c\GenerateEvaluationBundle\Services\ChartDataSetConfig
 */
class CategoryContext implements ChartDataSetConfigInterface
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
        return [
            "promotional_activity" => [
                "twig_name"    => "promotional-activity.json.twig",
                "data_service" => "extract_chart_data_set_promotional_activity",
            ],
            "sales_performance"    => [
                "twig_name"    => "sales-performance.json.twig",
                "data_service" => "extract_chart_data_set_sales_performance",
            ],
        ];
    }
}
