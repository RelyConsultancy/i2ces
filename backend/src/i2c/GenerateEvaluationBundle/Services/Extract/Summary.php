<?php

namespace i2c\GenerateEvaluationBundle\Services\Extract;

use Doctrine\DBAL\Connection;
use i2c\GenerateEvaluationBundle\Services\ExtractInterface;

/**
 * Class Summary
 *
 * @package i2c\GenerateEvaluationBundle\Services\Extract
 */
class Summary implements ExtractInterface
{
    /** @var Connection */
    protected $connection;

    /**
     * Summary constructor.
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
            'SELECT label, value, exposed, control, unit FROM
               (SELECT r.objective as label, r.uplift as value, r.exposed as exposed, r.control as control, u.unit as unit, r.obj_priority 
                FROM ie_results_data r JOIN i2c_objective_units u 
                ON r.metric=u.metric 
                WHERE master_campaign_id = \'%s\' 
                AND media_type=\'Total\' AND product = \'Offer\' 
                AND timeperiod = 2 
                UNION ALL 
                  SELECT r.objective as label, r.uplift as value, r.exposed as exposed, r.control as control, u.unit as unit, r.obj_priority 
                  FROM ie_results_data r JOIN i2c_objective_units u 
                  ON r.metric=u.metric 
                  WHERE master_campaign_id = \'%s\' 
                  AND media_type=\'Total\' AND product = \'Aisle\' 
                  AND timeperiod = 2 
                  AND r.objective = \'Grow total category\') x 
              ORDER BY obj_priority ASC;
            ',
            $cid, $cid
        );
    }
}
