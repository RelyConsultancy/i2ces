<?php

namespace i2c\GenerateEvaluationBundle\Services\ChartDataSetConfig;

use Doctrine\DBAL\Connection;
use i2c\GenerateEvaluationBundle\Services\ChartDataSetConfigInterface;

/**
 * Class ObjectiveReview
 *
 * @package i2c\GenerateEvaluationBundle\Services\ChartDataSetConfig
 */
class ObjectiveReview implements ChartDataSetConfigInterface
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
    public function getWeeklyUnitsSoldExposedAndControlConfig($cid)
    {
        return [
            "weekly_units_sold_exposed_and_control" => [
                "twig_name"    => "weekly-units-sold-exposed-and-control.json.twig",
                "data_service" => "extract_chart_data_set_weekly_units_sold_exposed_and-control",
            ],
        ];
    }

    /**
     * @param $cid
     *
     * @return array
     */
    public function getGrowTotalCategoryConfig($cid)
    {
        $query = sprintf(
            'SELECT COUNT(0) as count
             FROM ie_results_data
             WHERE media_type=\'Total\'
             AND objective=\'Grow total category\'
             AND metric=\'Known_spend\'
             AND product=\'Aisle\'
             AND timeperiod=2
             AND master_campaign_id=\'%s\'
            ',
            $cid
        );

        $result = $this->connection->fetchAll($query);

        if (1 > (int) $result[0]['count']) {
            return [];
        }

        $query = sprintf(
            'SELECT COUNT(0) as count
             FROM ie_results_data
             WHERE media_type=\'Total\'
             AND objective=\'Grow total category\'
             AND metric=\'Known_spend\'
             AND product=\'Aisle\'
             AND timeperiod=3
             AND master_campaign_id=\'%s\'
            ',
            $cid
        );

        $result = $this->connection->fetchAll($query);

        if (1 > (int) $result[0]['count']) {
            return [];
        }

        $query = sprintf(
            'SELECT COUNT(0) as count
             FROM ie_weekly_results_data
             WHERE master_campaign_id = \'%s\'
             AND product=\'Aisle\'
             AND metric=\'Known_spend\'
            ',
            $cid
        );

        $result = $this->connection->fetchAll($query);

        if (1 > (int) $result[0]['count']) {
            return [];
        }

        return [
            "grow_total_category" => [
                "twig_name"    => "grow-total-category.json.twig",
                "data_service" => "extract_chart_data_set_grow_total_category",
            ],
        ];
    }

    /**
     * @param $cid
     *
     * @return array
     */
    public function getMediaTypeCombinationsConfig($cid)
    {
        $query = sprintf(
            'SELECT count(0) AS count FROM ie_exposed_data WHERE master_campaign_id=\'%s\' and media_type <> \'Total\'',
            $cid
        );

        $result = $this->connection->fetchAll($query);

        if (2 > (int) $result[0]['count']) {
            return [];
        }

        return [
            "media_type_combinations" => [
                "twig_name"    => "media-type-combinations.json.twig",
                "data_service" => "extract_chart_data_set_media_type_combinations",
            ],
        ];
    }
}
