<?php

namespace i2c\GenerateEvaluationBundle\Services\Extract\TableData\CategoryContext;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\DBAL\Connection;
use i2c\GenerateEvaluationBundle\Services\ExtractInterface;

/**
 * Class SalesPerformance
 *
 * @package i2c\GenerateEvaluationBundle\Services\Extract\TableData\CategoryContext
 */
class SalesPerformance implements ExtractInterface
{
    /** @var Connection */
    protected $connection;

    /**
     * CampaignBackground constructor.
     *
     * @param Registry $registry
     */
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

    public function getTableData($cid)
    {
        return sprintf(
            'SELECT product as product, metric as metric, pp_results as results from
             ie_cat_context_data where master_campaign_id = \'%s\' order by product
            ',
            $cid
        );
    }
}
