<?php

namespace Evaluation\EvaluationBundle\Services;

use Doctrine\DBAL\Exception\DriverException;
use Doctrine\DBAL\Exception\InvalidFieldNameException;
use Evaluation\SetupBundle\Services\AbstractSchemaUpdateService;

class MediumSchemaUpdateService extends AbstractSchemaUpdateService
{
    /**
     * Creates the table if it does not exist
     */
    protected function createTable()
    {
        $query = sprintf(
            'CREATE TABLE IF NOT EXISTS `%s` (
                id INT(11) NOT NULL AUTO_INCREMENT,
                uid VARCHAR(255) NOT NULL,
                label VARCHAR(255) NOT NULL,
                PRIMARY KEY (id)
            )',
            $this->tableName
        );

        $connection = $this->entityManager->getConnection();
        $connection->exec($query);
    }

    /**
     * Updates the table schema
     */
    protected function updateTable()
    {
        $connection = $this->entityManager->getConnection();
        try {
            $query = sprintf(
                'ALTER TABLE `%s` CHANGE medium_name label VARCHAR(255) NOT NULL',
                $this->tableName
            );

            $connection->exec($query);
        } catch (InvalidFieldNameException $ex) {
        }
        try {
            $query = sprintf(
                'ALTER TABLE `%s` DROP COLUMN uid',
                $this->tableName
            );

            $connection->exec($query);
        } catch (DriverException $ex) {
        }
    }
}
