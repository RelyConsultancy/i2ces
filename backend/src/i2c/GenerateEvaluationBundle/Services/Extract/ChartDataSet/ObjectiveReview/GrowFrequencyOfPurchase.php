<?php

namespace i2c\GenerateEvaluationBundle\Services\Extract\ChartDataSet\ObjectiveReview;

use Doctrine\DBAL\Connection;
use i2c\GenerateEvaluationBundle\Services\ExtractInterface;

/**
 * Class GrowTotalCategory
 *
 * @package i2c\GenerateEvaluationBundle\Services\Extract\ChartDataSetConfig\ObjectiveReview
 */
class GrowFrequencyOfPurchase implements ExtractInterface
{
    /** @var Connection */
    protected $connection;

    /**
     * GrowTotalCategory constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Calls all the function in the class that begin with "get"
     *
     * @param string $cid
     *
     * @return array
     */
    public function fetchAll($cid)
    {
        $result = [];
        $methods = get_class_methods($this);
        foreach ($methods as $method) {
            if ('get' !== substr($method, 0, 3)) {
                continue;
            }
            $sql = call_user_func_array(array($this, $method), [$cid]);
            $methodName = substr($method, 3);

            $underscoreMethodName = $string = preg_replace('/(?<=\\w)(?=[A-Z])/', "_$1", $methodName);
            $underscoreMethodName = strtolower($underscoreMethodName);

            $result[$underscoreMethodName] = $this->connection->fetchAll($sql);
        }

        return $result;
    }

    /**
     * @param string $cid
     *
     * @return string
     */
    public function getOfferData($cid)
    {
        return sprintf(
            'SELECT timeperiod AS timeperiod, exposed AS exposed, control AS control
             FROM ie_results_data
             WHERE master_campaign_id = \'%s\'
             AND product=\'Offer\'
             AND media_type=\'Total\'
             AND timeperiod IN (2,3)
             AND metric=\'FOP\'
             AND objective=\'Grow frequency of shop per customer\';
            ',
            $cid
        );
    }
    
    /**
     * @param string $cid
     *
     * @return string
     */
    public function getBrandData($cid)
    {
        return sprintf(
            'SELECT timeperiod AS timeperiod, exposed AS exposed, control AS control
             FROM ie_results_data
             WHERE master_campaign_id = \'%s\'
             AND product=\'Brand\'
             AND media_type=\'Total\'
             AND timeperiod IN (2,3)
             AND metric=\'FOP\'
             AND objective=\'Grow frequency of shop per customer\';
            ',
            $cid
        );
    }

    /**
     * Returns an array with start and end dates for pre timings.
     *
     * @param $cid
     *
     * @return string
     */
    public function getDuringCombinations($cid)
    {
        return sprintf(
            'SELECT r.media_type AS media_type, r.uplift AS uplift, r.pct_uplift AS percentage_uplift, r.control as control, e.exposed AS exposed
             FROM ie_results_data AS r
             JOIN ie_exposed_data AS e ON e.media_type=r.media_type
             AND e.master_campaign_id=r.master_campaign_id
             WHERE r.media_type <> \'Total\'
             AND r.media_type <> \'Other\'
             AND r.timeperiod=2
             AND r.metric=\'FOP\'
             AND r.objective=\'Grow frequency of shop per customer\'
             AND r.product=\'Offer\'
             AND r.master_campaign_id=\'%s\' GROUP BY e.media_type;
            ',
            $cid
        );
    }

    
    /**
     * Returns an array with start and end dates for pre timings.
     *
     * @param $cid
     *
     * @return string
     */
    public function getTimingPre($cid)
    {
        return sprintf(
            'SELECT t1.period_date AS start_date, t2.period_date AS end_date
             FROM ie_timings_data AS t1
             JOIN ie_timings_data AS t2 ON (t1.master_campaign_id = t2.master_campaign_id)
             WHERE t1.master_campaign_id = \'%s\' AND t1.period = 1 AND t2.period = 2
            ',
            $cid
        );
    }

    /**
     * Returns an array with start and end dates for during timings.
     *
     * @param $cid
     *
     * @return string
     */
    public function getTimingDuring($cid)
    {
        return sprintf(
            'SELECT t1.period_date AS start_date, t2.period_date AS end_date
             FROM ie_timings_data AS t1
             JOIN ie_timings_data AS t2 ON (t1.master_campaign_id = t2.master_campaign_id)
             WHERE t1.master_campaign_id = \'%s\' AND t1.period = 3 AND t2.period = 4
            ',
            $cid
        );
    }

    /**
     * Returns an array with start and end dates for post timings.
     *
     * @param $cid
     *
     * @return string
     */
    public function getTimingPost($cid)
    {
        return sprintf(
            'SELECT t1.period_date AS start_date, t2.period_date AS end_date
             FROM ie_timings_data AS t1
             JOIN ie_timings_data AS t2 ON (t1.master_campaign_id = t2.master_campaign_id)
             WHERE t1.master_campaign_id = \'%s\' AND t1.period = 5 AND t2.period = 6
            ',
            $cid
        );
    }
}
