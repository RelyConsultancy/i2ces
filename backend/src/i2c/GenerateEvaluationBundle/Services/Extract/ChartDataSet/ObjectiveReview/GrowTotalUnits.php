<?php

namespace i2c\GenerateEvaluationBundle\Services\Extract\ChartDataSet\ObjectiveReview;

use Doctrine\DBAL\Connection;
use i2c\GenerateEvaluationBundle\Services\ExtractInterface;

/**
 * Class GrowTotalCategory
 *
 * @package i2c\GenerateEvaluationBundle\Services\Extract\ChartDataSetConfig\ObjectiveReview
 */
class GrowTotalUnits implements ExtractInterface
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
        foreach ($methods as$method) {
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
    public function getData($cid)
    {
        return sprintf(
            'SELECT week_commencing AS start_date, exposed AS exposed, control AS control
             FROM ie_weekly_results_data
             WHERE master_campaign_id = \'%s\'
             AND product=\'Offer\'
             AND metric=\'Units\';
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
    public function getDuring($cid)
    {
        return sprintf(
            'SELECT uplift AS uplift, pct_uplift AS percentage_uplift
             FROM ie_results_data
             WHERE media_type=\'Total\'
             AND objective=\'Grow total units\'
             AND metric=\'Units\'
             AND product=\'Offer\'
             AND timeperiod=2
             AND master_campaign_id=\'%s\'
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
    public function getPost($cid)
    {
        return sprintf(
            'SELECT uplift AS uplift, pct_uplift AS percentage_uplift
             FROM ie_results_data
             WHERE media_type=\'Total\'
             AND objective=\'Grow total units\'
             AND metric=\'Units\'
             AND product=\'Offer\'
             AND timeperiod=3
             AND master_campaign_id=\'%s\'
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
    public function getTotal($cid)
    {
        return sprintf(
            'SELECT SUM(uplift) AS uplift, SUM(uplift) / SUM(control) AS percentage_uplift
             FROM ie_results_data
             WHERE media_type=\'Total\'
             AND objective=\'Grow total units\'
             AND metric=\'Units\'
             AND product=\'Offer\'
             AND master_campaign_id=\'%s\'
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
    
    public function getObjectivePriority ($cid)
    {
        return sprintf(
                'SELECT DISTINCT obj_priority from ie_results_data
                 WHERE objective=\'Grow total units\'
                 AND master_campaign_id=\'%s\'
                ', $cid); 
    } 
}
