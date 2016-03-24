<?php

namespace i2c\GenerateEvaluationBundle\Services\ChartDataSetConfig;

use i2c\GenerateEvaluationBundle\Services\ChartDataSetConfigInterface;

/**
 * Class ObjectiveReview
 *
 * @package i2c\GenerateEvaluationBundle\Services\ChartDataSetConfig
 */
class ObjectiveReview implements ChartDataSetConfigInterface
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
            "grow_total_category" => [
                "twig_name"    => "grow-total-category.json.twig",
                "data_service" => "extract_chart_data_set_grow_total_category",
            ],
        ];
    }
}
