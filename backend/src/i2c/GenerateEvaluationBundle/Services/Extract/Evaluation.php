<?php

namespace i2c\GenerateEvaluationBundle\Services\Extract;

use Doctrine\DBAL\Connection;
use i2c\GenerateEvaluationBundle\Services\ExtractInterface;

/**
 * Class Evaluation
 *
 * @package i2c\GenerateEvaluationBundle\Services\Extract
 */
class Evaluation implements ExtractInterface
{
    /** @var Connection  */
    protected $connection;

    /**
     * Evaluation constructor.
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

    public function getGeneralData($cid)
    {
        return sprintf(
            'SELECT campaign_name as title, brand as brand, category as category from ie_campaign_data
             where master_campaign_id = \'%s\'
            ',
            $cid
        );
    }

    public function getStartDate($cid)
    {
        return sprintf(
            'SELECT period_date as date from ie_timings_data where master_campaign_id = \'%s\' AND period = 1',
            $cid
        );
    }

    /**
     * Returns an array of all the dates of the campaign in descending order the first value would be either the end
     * of the post period or the latest date provided for a given campaign, the decision on which date to use should be
     * handled in the twig file
     *
     * @param $cid
     *
     * @return string
     */
    public function getEndDate($cid)
    {
        return sprintf(
            'SELECT period_date as date from ie_timings_data where master_campaign_id = \'%s\' ORDER BY period DESC ',
            $cid
        );
    }

    public function getSupplier($cid)
    {
        return sprintf(
            'SELECT bu.id AS id FROM ie_campaign_data AS cd
             JOIN oro_business_unit AS bu ON (cd.supplier = bu.name)
             WHERE cd.master_campaign_id = \'%s\'',
            $cid
        );
    }

    public function getDefaultSupplier($cid)
    {
        return sprintf(
            'SELECT id as id from oro_business_unit where name=\'Main\'
            '
        );
    }
}
