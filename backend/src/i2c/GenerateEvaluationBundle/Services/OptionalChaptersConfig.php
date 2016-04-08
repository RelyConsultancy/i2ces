<?php

namespace i2c\GenerateEvaluationBundle\Services;

use Doctrine\DBAL\Connection;

/**
 * Class OptionalChaptersConfig
 *
 * @package i2c\GenerateEvaluationBundle\Services
 */
class OptionalChaptersConfig
{

    /** @var Connection */
    protected $connection;

    /**
     * EvaluationChapters constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Returns an array with optional chapters config data.
     *
     * @param string $cid
     *
     * @return array
     */
    public function fetchOptionalChaptersConfig($cid)
    {
        $result = [];
        $methods = get_class_methods($this);
        foreach ($methods as $method) {
            if ('get' !== substr($method, 0, 3)) {
                continue;
            }
            $config = call_user_func_array(array($this, $method), [$cid]);
            if (empty($config)) {
                continue;
            }

            $result[] = $config;
        }

        return $result;
    }

    /**
     * @param string $cid
     *
     * @return array
     */
    public function getSamplingPerformance($cid)
    {
        $query = sprintf(
            'SELECT COUNT(0) AS count FROM ie_media_data WHERE
             media_label LIKE \'%%sampling%%\' AND master_campaign_id = \'%s\'
            ',
            $cid
        );

        $result = $this->connection->fetchAll($query);

        if (1 > (int) $result[0]['count']) {
            return [];
        }

        return [
            "twig_name"                   => "sampling-performance.json.twig",
            "data_service"                => "extract_summary",
            "chart_data_set_service_name" => "data_set_config_summary",
            "additional_data"             => [
                "title" => "Sampling Performance",
                "state" => "visible",
                "order" => 3,

            ],
        ];
    }
}
