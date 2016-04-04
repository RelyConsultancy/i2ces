<?php

namespace i2c\GeneratePdfBundle\Services;

use Doctrine\DBAL\Connection;

/**
 * Class EvaluationQueue
 *
 * @package i2c\GeneratePdfBundle\Services
 */
class EvaluationQueue
{
    protected $connection;

    /**
     * EvaluationQueue constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return array
     */
    public function getEvaluationForGeneration()
    {
        $query = 'SELECT evaluation_cid as cid from i2c_generate_pdf_queue ORDER BY published_time';

        $result = $this->connection->fetchColumn($query);

        return $result;
    }

    /**
     * @param string $cid
     */
    public function removeFromQueue($cid)
    {
        $query = sprintf(
            'DELETE FROM i2c_generate_pdf_queue where evaluation_cid = \'%s\'',
            $cid
        );

        $this->connection->exec($query);
    }

    /**
     * @param $cid
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function insertToQueue($cid)
    {
        $now = new \DateTime('now');
        $query = sprintf(
            'INSERT INTO i2c_generate_pdf_queue(evaluation_cid,published_time) VALUES (\'%s\',\'%s\')',
            $cid,
            $now->format('Y-m-d\TH:i:s')
        );

        $this->connection->exec($query);
    }
}
