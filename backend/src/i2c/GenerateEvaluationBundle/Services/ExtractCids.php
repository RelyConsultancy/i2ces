<?php

namespace i2c\GenerateEvaluationBundle\Services;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use PDO;

/**
 * Class ExtractCids
 *
 * @package i2c\GenerateEvaluationBundle\Services
 */
class ExtractCids
{
    protected $cids;

    /** @var EntityManagerInterface */
    protected $entityManager;

    /**
     * ExtractCids constructor.
     *
     * @param Registry $doctrine
     */
    public function __construct(Registry $doctrine)
    {
        $this->entityManager = $doctrine->getEntityManager();
    }

    /**
     * Returns an array of campaign ids to be generated.
     *
     * @return array
     *
     * @throws DBALException
     */
    public function getAvailableCampaignCids()
    {
        /** @var Connection $connection */
        $connection = $this->entityManager->getConnection();
        $query = 'SELECT master_campaign_id
                  FROM ie_campaign_data
                 ';
        $response = $connection->query($query)->fetchAll(PDO::FETCH_COLUMN);

        return $response;
    }

    /**
     * Check if there is incomplete campaign data.
     *
     * @param array $cids
     *
     * @return array
     */
    public function validateCids($cids, $includeExisting)
    {
        $extraCondition = '';

        if (count($cids) < 0) {
            return $cids;
        }

        if (!$includeExisting) {
            $extraCondition = 'and cd.master_campaign_id NOT IN (
                    SELECT cid FROM evaluation
                  )
                ';
        }

        /** @var Connection $connection */
        $connection = $this->entityManager->getConnection();
        $query = sprintf(
            'SELECT DISTINCT cd.master_campaign_id
             FROM ie_campaign_data AS cd
             JOIN ie_results_data AS rd ON (cd.master_campaign_id = rd.master_campaign_id)
              WHERE cd.master_campaign_id IN (%s) %s
            ',
            sprintf("'%s'", implode("','", $cids)),
            $extraCondition
        );
        $cids = $connection->query($query)->fetchAll(PDO::FETCH_COLUMN);

        return $cids;
    }

    /**
     * Returns an array of campaign ids that can be generated.
     *
     * @param array $cids
     * @param bool  $includeExisting
     *
     * @return array
     */
    public function getCampaignCidsToBeGenerated($cids = array(), $includeExisting = false)
    {
        if (empty($cids)) {
            $cids = $this->getAvailableCampaignCids();
        }

        $cids = $this->validateCids($cids, $includeExisting);

        return $cids;
    }
}
