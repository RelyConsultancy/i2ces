<?php

namespace i2c\GenerateEvaluationBundle\Services;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use PDO;

/**
 * Class ExtractCids
 *
 * @package i2c\GenerateEvaluationBundle\Services
 */
class ExtractCids
{
    /** @var Connection  */
    protected $connection;

    /**
     * ExtractCids constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    /**
     * Returns an array of campaign ids that can be generated.
     *
     * @param array $cids            When empty, all the campaign ids will be used for the validation of eligible
     *                               campaigns for generation, otherwise only the sent ones will be used
     * @param bool  $includeExisting If false it will exclude the campaign ids that have already been generated
     *
     * @return array
     */
    public function getCampaignIdsEligibleForBeGenerated($cids = array(), $includeExisting = false)
    {
        if (empty($cids)) {
            $cids = $this->getAllImportedCampaignIds();
        }

        $cids = $this->filterCampaignIds($cids, $includeExisting);

        return $cids;
    }

    /**
     * Returns an array of all the imported campaign ids.
     *
     * @return array
     *
     * @throws DBALException
     */
    protected function getAllImportedCampaignIds()
    {
        $query = 'SELECT master_campaign_id
                  FROM ie_campaign_data
                 ';
        $response = $this->connection->query($query)->fetchAll(PDO::FETCH_COLUMN);

        return $response;
    }

    /**
     * Filters the campaign ids sent, excluding the ones that don't have enough data to be generated and in case the
     * 'includeExisting' is sent as false, it will exclude the campaign ids that already have an evaluation generated
     * for them
     *
     * @param array $cids
     *
     * @return array
     */
    protected function filterCampaignIds($cids, $includeExisting)
    {
        $extraCondition = '';

        if (count($cids) < 0) {
            return $cids;
        }

        if (!$includeExisting) {
            $extraCondition = 'AND cd.master_campaign_id NOT IN (
                    SELECT cid FROM evaluation
                  )
                ';
        }

        $query = sprintf(
            'SELECT DISTINCT cd.master_campaign_id
             FROM ie_campaign_data AS cd
             JOIN ie_results_data AS rd ON (cd.master_campaign_id = rd.master_campaign_id)
              WHERE cd.master_campaign_id IN (%s) %s
            ',
            sprintf('\'%s\'', implode('\',\'', $cids)),
            $extraCondition
        );
        $cids = $this->connection->query($query)->fetchAll(PDO::FETCH_COLUMN);

        return $cids;
    }
}
