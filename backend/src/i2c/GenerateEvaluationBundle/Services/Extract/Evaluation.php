<?php

namespace i2c\GenerateEvaluationBundle\Services\Extract;

use Doctrine\Bundle\DoctrineBundle\Registry;
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
            'SELECT period_date as date from ie_timings_data where master_campaign_id = \'%s\' and period = 1',
            $cid
        );
    }

    public function getEndDate($cid)
    {
        return sprintf(
            'SELECT period_date as date from ie_timings_data where master_campaign_id = \'%s\' and period = 2',
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
}
