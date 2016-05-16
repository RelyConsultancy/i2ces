<?php

namespace i2c\GenerateEvaluationBundle\Services\Extract\ChartDataSet\ObjectiveReview;

use Doctrine\DBAL\Connection;
use i2c\GenerateEvaluationBundle\Services\ExtractInterface;

/**
 * Class MediaTypeCombinations
 *
 * @package i2c\GenerateEvaluationBundle\Services\Extract\ChartDataSet\ObjectiveReview
 */
class MediaTypeCombinations implements ExtractInterface
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
     * @param $cid
     *
     * @return string
     */
    public function getData($cid)
    {
        return sprintf(
            'SELECT r.media_type AS media_type, r.uplift AS uplift, r.pct_uplift AS percentage_uplift, e.exposed AS exposed
             FROM ie_results_data AS r
             JOIN ie_exposed_data AS e ON (e.media_type=r.media_type)
             WHERE r.media_type <> \'Total\'
             AND r.media_type <> \'Other\'
             AND r.timeperiod=2
             AND r.metric=\'Units\'
             AND r.product=\'Offer\'
             AND r.master_campaign_id=\'%s\' GROUP BY e.media_type;
            ',
            $cid
        );
    }
}
