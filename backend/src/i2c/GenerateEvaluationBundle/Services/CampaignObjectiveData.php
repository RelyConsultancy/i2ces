<?php

namespace i2c\GenerateEvaluationBundle\Services;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use PDO;

/**
 * Class CampaignObjectiveData
 *
 * @package i2c\GenerateEvaluationBundle\Services
 */
class CampaignObjectiveData
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /**
     * CampaignObjectiveDataService constructor.
     *
     * @param Registry $doctrine
     */
    public function __construct(Registry $doctrine)
    {
        $this->entityManager = $doctrine->getEntityManager();
    }

    /**
     * Returns an array or campaign ids.
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getCampaignCids()
    {
        /** @var Connection $connection */
        $connection = $this->entityManager->getConnection();
        $query = 'SELECT master_campaign_id, category, campaign_name, supplier, brand, npd, media_cost
                  FROM ie_campaign_data ';
        $response = $connection->query($query)->fetchAll();

        return $response;
    }

    /**
     * Returns an array of objective titles.
     *
     * @param string $cid
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getObjectives($cid)
    {
        /** @var Connection $connection */
        $connection = $this->entityManager->getConnection();
        $query = sprintf(
            'SELECT objective
              FROM ie_results_data
              WHERE master_campaign_id = \'%s\' AND media_type=\'Total\' AND product = \'Offer\' AND obj_priority <> 0
              AND timeperiod = 2',
            $cid
        );
        $response = $connection->query($query)->fetchAll(PDO::FETCH_COLUMN);

        return $response;
    }

    /**
     * Returns an array of objective titles.
     *
     * @param string $cid
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getObjectivesComplete($cid)
    {
        /** @var Connection $connection */
        $connection = $this->entityManager->getConnection();
        $query = sprintf(
            'SELECT objective, uplift
              FROM ie_results_data
              WHERE master_campaign_id = \'%s\' AND media_type=\'Total\' AND product = \'Offer\' AND obj_priority <> 0
              AND timeperiod = 2
              ORDER BY obj_priority ASC
            ',
            $cid
        );
        $response = $connection->query($query)->fetchAll();

        return $response;
    }

    /**
     * Returns and array of periods and period dates.
     *
     * @param string $cid
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getTiming($cid)
    {
        /** @var Connection $connection */
        $connection = $this->entityManager->getConnection();
        $format = '%d/%m/%Y';
        $query = sprintf(
            'SELECT period, period_date
              FROM ie_timings_data
              WHERE master_campaign_id = \'%s\'
              ORDER BY period,
               STR_TO_DATE(period_date, \'%s\')',
            $cid,
            $format
        );
        $response = $connection->query($query)->fetchAll();

        return $response;
    }

    /**
     * Returns the cost of an evaluation.
     *
     * @param string $cid
     *
     * @return int
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getEvaluationCost($cid)
    {
        /** @var Connection $connection */
        $connection = $this->entityManager->getConnection();
        $query = sprintf(
            'SELECT media_cost
              FROM ie_campaign_data
              WHERE master_campaign_id = \'%s\'',
            $cid
        );
        $response = $connection->query($query)->fetchColumn();

        return (int)$response;
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
        $connection = $this->entityManager->getConnection();

        $query = sprintf(
            'SELECT media_label,
             media,
             start_date,
             end_date
            FROM ie_media_data
            WHERE master_campaign_id = \'%s\'',
            $cid
        );
        $response = $connection->query($query)->fetchAll();

        return $response;
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
        $connection = $this->entityManager->getConnection();

        $queryExpression = "CONCAT(LEFT(media, CHAR_LENGTH(media) - 1),
        IF(RIGHT(media, 1) REGEXP '[0-9]' = 0, RIGHT(media, 1), ''))";

        $query = sprintf(
            'SELECT DISTINCT %s as media
            FROM ie_media_data
            WHERE master_campaign_id = \'%s\'',
            $queryExpression,
            $cid
        );
        $response = $connection->query($query)->fetchAll(PDO::FETCH_COLUMN);

        return $response;
    }
}
