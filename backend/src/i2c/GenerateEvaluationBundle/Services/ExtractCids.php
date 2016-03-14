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
                  WHERE master_campaign_id NOT IN (
                    SELECT cid FROM evaluation
                  )';
        $response = $connection->query($query)->fetchAll(PDO::FETCH_COLUMN);

        return $response;
    }

    /**
     * Check if there is incomplete campaign data.
     * @TODO define this method.
     *
     * @param array $cids
     *
     * @return array
     */
    public function validateCids($cids)
    {
        return $cids;
    }

    /**
     * Returns an array of campaign ids that can be generated.
     *
     * @return array
     */
    public function getCampaignCidsToBeGenerated()
    {
        $cids = $this->getAvailableCampaignCids();
        if (count($cids) > 0) {
            $cids = $this->validateCids($cids);
        }

        return $cids;
    }
}
