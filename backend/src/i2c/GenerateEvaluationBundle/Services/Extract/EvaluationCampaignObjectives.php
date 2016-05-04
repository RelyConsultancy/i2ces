<?php

namespace i2c\GenerateEvaluationBundle\Services\Extract;

use Doctrine\DBAL\Connection;
use i2c\GenerateEvaluationBundle\Services\ExtractInterface;

/**
 * Class EvaluationCampaignObjectives
 *
 * @package i2c\GenerateEvaluationBundle\Services\Extract
 */
class EvaluationCampaignObjectives implements ExtractInterface
{
    /** @var Connection */
    protected $connection;

    /**
     * EvaluationCampaignObjectives constructor.
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

    public function getObjectives($cid)
    {
        return sprintf(
            'SELECT r.objective as label, r.uplift as value, r.exposed as exposed, r.control as control, u.unit as unit
              FROM ie_results_data r join i2c_objective_units u on r.metric=u.metric
              WHERE master_campaign_id = \'%s\' AND media_type=\'Total\' AND product = \'Offer\'
              AND timeperiod = 2
              ORDER BY obj_priority ASC
            ',
            $cid
        );
    }
}
