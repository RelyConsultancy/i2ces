<?php

namespace i2c\GenerateEvaluationBundle\Services\Extract\ChartDataSet\CategoryContext;

use Doctrine\DBAL\Connection;
use i2c\GenerateEvaluationBundle\Services\ExtractInterface;

/**
 * Class PromotionalActivity
 *
 * @package i2c\GenerateEvaluationBundle\Services\Extract\ChartDataSetConfig\CategoryContext
 */
class PromotionalActivity implements ExtractInterface
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
     * @param string $cid
     *
     * @return string
     */
    public function getTableData($cid)
    {
        return sprintf(
            'SELECT week_commencing AS start_date, product AS product, pr_results AS results from
             ie_promo_data WHERE master_campaign_id = \'%s\'
            ',
            $cid
        );
    }
}
