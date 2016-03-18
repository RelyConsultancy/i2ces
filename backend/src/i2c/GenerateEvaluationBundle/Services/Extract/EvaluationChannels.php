<?php

namespace i2c\GenerateEvaluationBundle\Services\Extract;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\DBAL\Connection;
use i2c\GenerateEvaluationBundle\Services\ExtractInterface;

/**
 * Class EvaluationChapters
 *
 * @package i2c\GenerateEvaluationBundle\Services\Extract
 */
class EvaluationChannels implements ExtractInterface
{
    /** @var Connection  */
    protected $connection;

    /**
     * EvaluationChapters constructor.
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

    public function getChannels($cid)
    {
        return sprintf(
            'SELECT distinct media_label as label
             from ie_media_data
             where master_campaign_id = \'%s\'
            ',
            $cid
        );
    }
}
