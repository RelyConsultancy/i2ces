<?php

namespace i2c\GenerateEvaluationBundle\Services\Extract;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\DBAL\Connection;
use i2c\GenerateEvaluationBundle\Services\ExtractInterface;

/**
 * Class CampaignBackground
 *
 * @package i2c\GenerateEvaluationBundle\Services\Extract
 */
class CampaignBackground implements ExtractInterface
{
    /** @var Connection  */
    protected $connection;

    public function __construct(Registry $registry)
    {
        $this->connection = $registry->getEntityManager()->getConnection();
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
            $sql = call_user_func_array(array($this, $method), $cid);
            $methodName = substr($method, 3);

            $result[$methodName] = $this->connection->fetchAll($sql);
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
            'SELECT objective
             FROM ie_results_data
             WHERE master_campaign_id = \'%s\' AND media_type=\'Total\' AND product = \'Offer\' AND obj_priority <> 0
             AND timeperiod = 2
            ',
            $cid
        );
    }

    /**
     * @param $cid
     *
     * @return string
     */
    public function getObjectivesComplete($cid)
    {
        return sprintf(
            'SELECT objective, uplift
             FROM ie_results_data
             WHERE master_campaign_id = \'%s\' AND media_type=\'Total\' AND product = \'Offer\' AND obj_priority <> 0
             AND timeperiod = 2
             ORDER BY obj_priority ASC
            ',
            $cid
        );
    }

    /**
     * @param $cid
     *
     * @return string
     */
    public function getTiming($cid)
    {
        return sprintf(
            'SELECT period, period_date
             FROM ie_timings_data
             WHERE master_campaign_id = \'%s\'
             ORDER BY period,
             STR_TO_DATE(period_date, \'%s\')
            ',
            $cid,
            '%d/%m/%Y'
        );
    }

    /**
     * @param $cid
     *
     * @return string
     */
    public function getEvaluationCost($cid)
    {
        return sprintf(
            'SELECT media_cost
             FROM ie_campaign_data
             WHERE master_campaign_id = \'%s\'
            ',
            $cid
        );
    }

    /**
     * Returns an array with the media type and periods.
     *
     * @param $cid
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getMediaLaydownRows($cid)
    {
        return sprintf(
            'SELECT CONCAT(LEFT(media, CHAR_LENGTH(media) - 1), IF(RIGHT(media, 1) REGEXP \'[0-9]\' = 0,
             RIGHT(media, 1), \'\')) AS media,
             start_date,
             end_date
             FROM ie_media_data
             WHERE master_campaign_id = \'%s\'
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
    public function getEvaluationChannels($cid)
    {
        return sprintf(
            'SELECT DISTINCT CONCAT(LEFT(media, CHAR_LENGTH(media) - 1),
             IF(RIGHT(media, 1) REGEXP \'[0-9]\' = 0, RIGHT(media, 1), \'\')) as media
             FROM ie_media_data
             WHERE master_campaign_id = \'%s\'
            ',
            $cid
        );
    }
}
