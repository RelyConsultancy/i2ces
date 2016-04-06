<?php

namespace i2c\GenerateEvaluationBundle\Services\Extract;

use Doctrine\DBAL\Connection;
use i2c\GenerateEvaluationBundle\Services\ExtractInterface;

/**
 * Class CampaignBackground
 *
 * @package i2c\GenerateEvaluationBundle\Services\Extract
 */
class CampaignBackground implements ExtractInterface
{
    /** @var Connection */
    protected $connection;

    /**
     * CampaignBackground constructor.
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
     * Returns an array of objective titles.
     *
     * @param string $cid
     *
     * @return string
     */
    public function getObjectives($cid)
    {
        return sprintf(
            'SELECT objective as label
             FROM ie_results_data
             WHERE master_campaign_id = \'%s\' AND media_type=\'Total\' AND product = \'Offer\' AND obj_priority <> 0
             AND timeperiod = 2
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

    /**
     * Returns an array with evaluation cost.
     *
     * @param $cid
     *
     * @return string
     */
    public function getEvaluatedCost($cid)
    {
        return sprintf(
            'SELECT media_cost as cost
             FROM ie_campaign_data
             WHERE master_campaign_id = \'%s\'
            ',
            $cid
        );
    }

    /**
     * @param $cid
     *
     * @return string
     */
    public function getEvaluatedMediaLaydown($cid)
    {
        return sprintf(
            'SELECT \'media\' AS type,
             media_label AS media_label,
             start_date AS start_date,
             end_date AS end_date
             FROM ie_media_data
             WHERE master_campaign_id = \'%s\'
              AND evaluate = 1
            ',
            $cid
        );
    }

    /**
     * @param $cid
     *
     * @return string
     */
    public function getOfferMediaLaydown($cid)
    {
        return sprintf(
            'SELECT \'promotion\' AS type,
             media_label AS media_label,
             start_date AS start_date,
             end_date AS end_date
             FROM ie_media_data
             WHERE master_campaign_id = \'%s\'
              AND evaluate = 0
              AND media_label LIKE \'%%sampling%%\'
            ',
            $cid
        );
    }

    /**
     * @param $cid
     *
     * @return string
     */
    public function getOtherMediaLaydown($cid)
    {
        return sprintf(
            'SELECT \'other\' AS type,
             media_label AS media_label,
             start_date AS start_date,
             end_date AS end_date
             FROM ie_media_data
             WHERE master_campaign_id = \'%s\'
              AND evaluate = 0
              AND media_label NOT LIKE \'%%sampling%%\'
            ',
            $cid
        );
    }

    /**
     * Returns an array with media types.
     *
     * @param $cid
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getChannels($cid)
    {
        return sprintf(
            'SELECT DISTINCT m.media_label AS channel, i.icon_name AS icon
             FROM ie_media_data AS m JOIN i2c_channel_icons AS i ON (m.media_label = i.channel_name)
             WHERE master_campaign_id = \'%s\'
            ',
            $cid
        );
    }

    /**
     * @param $cid
     *
     * @return string
     */
    public function getProductDefinitions($cid)
    {
        return sprintf(
            'SELECT sku_name as sku from ie_offer_data where master_campaign_id = \'%s\'',
            $cid
        );
    }
}
