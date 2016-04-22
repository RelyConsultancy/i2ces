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

        $query = sprintf(
            'SELECT COUNT(0) as count
             FROM ie_results_data
             WHERE media_type=\'Total\'
             AND objective=\'Overview\'
             AND metric=\'Known_spend\'
             AND product=\'Offer\'
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
             AND objective=\'Overview\'
             AND metric=\'Known_spend\'
             AND product=\'Offer\'
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
             AND product=\'Offer\'
             AND metric=\'Units\'
            ',
            $cid
        );

        $result = $this->connection->fetchAll($query);

        if (1 > (int) $result[0]['count']) {
            return [];
        }

        return [
            "weekly_units_sold_exposed_and_control" => [
                "twig_name"    => "weekly-units-sold-exposed-and-control.json.twig",
                "data_service" => "extract_chart_data_set_weekly_units_sold_exposed_and_control",
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
            'SELECT count(*) AS count FROM ie_exposed_data WHERE master_campaign_id=\'%s\' AND media_type NOT IN (\'Total\', \'Other\')',
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
    
    /**
     * @param $cid
     *
     * @return array
     */
    public function getLaunchNewProductConfig($cid)
    {
        $query = sprintf(
            'SELECT COUNT(0) as count
             FROM ie_results_data
             WHERE media_type=\'Total\'
             AND objective=\'Launch new product\'
             AND metric=\'New_custs\'
             AND product=\'Offer\'
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
             AND objective=\'Launch new product\'
             AND metric=\'New_custs\'
             AND product=\'Offer\'
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
             AND product=\'Offer\'
             AND metric=\'Units\'
            ',
            $cid
        );

        $result = $this->connection->fetchAll($query);

        if (1 > (int) $result[0]['count']) {
            return [];
        }

        return [
            "launch_new_product" => [
                "twig_name"    => "launch-new-product.json.twig",
                "data_service" => "extract_chart_data_set_launch_new_product",
            ],
        ];
    }
    
    public function getAcquireNewCustomersConfig ($cid)
    {
        
        if ( $this->checkDoubleChartConfig($cid, 'Acquire new customers', 'New_custs') )
        {
            return [
                "acquire_new_customers" => [
                    "twig_name"    => "acquire-new-customers.json.twig",
                    "data_service" => "extract_chart_data_set_acquire_new_customers",
                ],
            ];
        }
        
        return [];
        
    }
    
    public function getRetainLapsingCustomersConfig ($cid)
    {
        
        if ( $this->checkDoubleChartConfig($cid, 'Retain lapsing customers', 'Lapsed_custs') )
        {
            return [
                "retain_lapsing_customers" => [
                    "twig_name"    => "retain-lapsed-customers.json.twig",
                    "data_service" => "extract_chart_data_set_retain_lapsed_customers",
                ],
            ];
        }
        
        return [];
        
    }
    
    public function getRetainNewCustomersTrialistsConfig ($cid)
    {
        
        if ( $this->checkDoubleChartConfig($cid, 'Retain new customers (trialists)', 'New_trialists') )
        {
            return [
                "retain_new_customers_trialists" => [
                    "twig_name"    => "retain-new-customers-trialists.json.twig",
                    "data_service" => "extract_chart_data_set_retain_new_customers_trialists",
                ],
            ];
        }
        
        return [];
        
    }
    
    public function getRetainExistingCustomersConfig ($cid)
    {
        
        if ( $this->checkDoubleChartConfig($cid, 'Retain existing customers', 'Existing_custs') )
        {
            return [
                "retain_existing_customers" => [
                    "twig_name"    => "retain-existing-customers.json.twig",
                    "data_service" => "extract_chart_data_set_retain_existing_customers",
                ],
            ];
        }
        
        return [];
        
    }
    
    public function getGrowSpendPerExistingCustomerConfig ($cid)
    {
        
        if ( $this->checkDoubleChartConfig($cid, 'Grow spend per existing customer', 'SPEC') )
        {
            return [
                "grow_spend_per_existing_customer" => [
                    "twig_name"    => "grow-spend-per-existing-customer.json.twig",
                    "data_service" => "extract_chart_data_set_grow_spend_per_existing_customer",
                ],
            ];
        }
        
        return [];
        
    }
    
    public function getGrowUnitsPerExistingCustomerConfig ($cid)
    {
        
        if ( $this->checkDoubleChartConfig($cid, 'Grow units per existing customer', 'UPEC') )
        {
            return [
                "grow_units_per_existing_customer" => [
                    "twig_name"    => "grow-units-per-existing-customer.json.twig",
                    "data_service" => "extract_chart_data_set_grow_units_per_existing_customer",
                ],
            ];
        }
        
        return [];
        
    }
    
    public function getGrowFrequencyOfSharePerCustomerConfig ($cid)
    {
        
        if ( $this->checkDoubleChartConfig($cid, 'Grow frequency of shop per customer', 'FOP') )
        {
            return [
                "grow_frequency_of_share_per_customer" => [
                    "twig_name"    => "grow-frequency-of-purchase.json.twig",
                    "data_service" => "extract_chart_data_set_grow_frequency_of_purchase",
                ],
            ];
        }
        
        return [];
        
    }
    
    public function getGrowCustomerProductRangeConfig ($cid)
    {
        
        if ( $this->checkDoubleChartConfig($cid, 'Grow customer product range (cross sell)', 'GCS_custs') )
        {
            return [
                "grow_customer_product_range" => [
                    "twig_name"    => "grow-cross-sell.json.twig",
                    "data_service" => "extract_chart_data_set_grow_cross_sell",
                ],
            ];
        }
        
        return [];
        
    }
    
    public function getGrowShareOfCategoryConfig ($cid)
    {
        
        if ( $this->checkDoubleChartConfig($cid, 'Grow share of category', 'SOW') )
        {
            return [
                "grow_share_of_category" => [
                    "twig_name"    => "grow-share-of-category.json.twig",
                    "data_service" => "extract_chart_data_set_grow_share_of_category",
                ],
            ];
        }
        
        return [];
        
    }
    
    private function checkDoubleChartConfig ($cid, $objective, $metric)
    {
        $query = sprintf(
            'SELECT COUNT(0) as count
             FROM ie_results_data
             WHERE media_type=\'Total\'
             AND objective=\'' . $objective . '\'
             AND metric=\'' . $metric . '\'
             AND product=\'Offer\'
             AND timeperiod=2
             AND master_campaign_id=\'%s\'
            ',
            $cid
        );
        
        $result = $this->connection->fetchAll($query);

        if (1 > (int) $result[0]['count']) {
            return false;
        }
        
        return true;
        /*
        $query = sprintf(
            'SELECT COUNT(0) as count
             FROM ie_results_data
             WHERE media_type=\'Total\'
             AND objective=\'' . $objective . '\'
             AND metric LIKE \'' . $metric . '\'
             AND product=\'Offer\'
             AND timeperiod=3
             AND master_campaign_id=\'%s\'
            ',
            $cid
        );
        
        $result = $this->connection->fetchAll($query);

        if (1 > (int) $result[0]['count']) {
            return false;
        }
        
        return true;
         * */
         
    }
}
