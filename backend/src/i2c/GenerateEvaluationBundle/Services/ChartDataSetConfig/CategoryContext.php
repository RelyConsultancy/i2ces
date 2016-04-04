<?php

namespace i2c\GenerateEvaluationBundle\Services\ChartDataSetConfig;

use Doctrine\DBAL\Connection;
use i2c\GenerateEvaluationBundle\Services\ChartDataSetConfigInterface;

/**
 * Class CategoryContext
 *
 * @package i2c\GenerateEvaluationBundle\Services\ChartDataSetConfig
 */
class CategoryContext implements ChartDataSetConfigInterface
{

    /** @var Connection */
    protected $connection;

    /**
     * EvaluationChapters constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Returns an array with table config data.
     *
     * @param string $cid
     *
     * @return array
     */
    public function fetchChartDataSetConfig($cid)
    {
        $result = [];
        $methods = get_class_methods($this);
        foreach ($methods as $method) {
            if ('get' !== substr($method, 0, 3)) {
                continue;
            }
            $config = call_user_func_array(array($this, $method), [$cid]);
            $result = array_merge($result, $config);
        }

        return $result;
    }

    /**
     * @param string $cid
     *
     * @return array
     */
    public function getPromotionalActivityConfig($cid)
    {
        return [
            "promotional_activity" => [
                "twig_name"    => "promotional-activity.json.twig",
                "data_service" => "extract_chart_data_set_promotional_activity",
            ],
        ];
    }

    /**
     * @param string $cid
     *
     * @return array
     */
    public function getSalesPerformanceConfig($cid)
    {
        return [
            "sales_performance" => [
                "twig_name"    => "sales-performance.json.twig",
                "data_service" => "extract_chart_data_set_sales_performance",
            ],
        ];
    }
}
