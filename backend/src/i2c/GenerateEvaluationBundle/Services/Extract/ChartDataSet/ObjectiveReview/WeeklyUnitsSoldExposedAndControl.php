<?php

namespace i2c\GenerateEvaluationBundle\Services\Extract\ChartDataSet\ObjectiveReview;

use Doctrine\DBAL\Connection;
use i2c\GenerateEvaluationBundle\Services\ExtractInterface;

/**
 * Class WeeklyUnitsSoldExposedAndControl
 *
 * @package i2c\GenerateEvaluationBundle\Services\Extract\ChartDataSet\ObjectiveReview
 */
class WeeklyUnitsSoldExposedAndControl implements ExtractInterface
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
     * @param $cid
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
             AND metric=\'Units\'
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
             AND objective=\'Overview\'
             AND metric=\'Known_spend\'
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
             AND objective=\'Overview\'
             AND metric=\'Known_spend\'
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
            'SELECT SUM(uplift) AS uplift, SUM(pct_uplift) AS percentage_uplift
             FROM ie_results_data
             WHERE media_type=\'Total\'
             AND objective=\'Overview\'
             AND metric=\'Known_spend\'
             AND product=\'Offer\'
             AND master_campaign_id=\'%s\'
            ',
            $cid
        );
    }
}
