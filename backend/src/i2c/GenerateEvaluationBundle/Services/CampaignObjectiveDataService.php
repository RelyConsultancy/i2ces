<?php

namespace i2c\GenerateEvaluationBundle\Services;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class CampaignObjectiveDataService
 *
 * @package i2c\GenerateEvaluationBundle\Services
 */
class CampaignObjectiveDataService
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

    public function getMediaLaydownHeaderPeriods($cid)
    {
        $connection = $this->entityManager->getConnection();
        $query = sprintf(
            'SELECT `period_date`
            FROM `ie_timings_data`
            WHERE `master_campaign_id` = %s
            ORDER BY `period` ASC',
            $cid
        );

        return $connection->exec($query);
    }

    public function getMediaLaydownChartPeriods($cid)
    {
        $connection = $this->entityManager->getConnection();

        $queryExpression = "CONCAT(LEFT(`media`, CHAR_LENGTH(`media`) - 1),
        IF(RIGHT(`media`, 1) REGEXP '[0-9]' = 0, RIGHT(`media`, 1), ''))";

        $query = sprintf(
            'SELECT %s, `start_date`, `end_date`
            FROM `ie_media_data`
            WHERE `evaluate` = 1
            AND `master_campaign_id` = %s',
            $queryExpression,
            $cid
        );

        return $connection->exec($query);
    }
}
