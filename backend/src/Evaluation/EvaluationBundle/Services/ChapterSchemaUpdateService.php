<?php

namespace Evaluation\EvaluationBundle\Services;

use Doctrine\DBAL\Exception\DriverException;
use i2c\SetupBundle\Services\AbstractSchemaUpdateService;

/**
 * Class ChapterSchemaUpdateService
 *
 * @package Evaluation\EvaluationBundle\Services
 */
class ChapterSchemaUpdateService extends AbstractSchemaUpdateService
{
    /**
     * Creates the table if it does not exist
     */
    protected function createTable()
    {
        $query = sprintf(
            'CREATE TABLE IF NOT EXISTS `%s` (
                id INT(11) NOT NULL AUTO_INCREMENT,
                title VARCHAR(255),
                state VARCHAR(255),
                location VARCHAR(255),
                content BLOB,
                created_at DATETIME,
                last_modified_at DATETIME,
                chapter_order INT,
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
                'ALTER TABLE `%s` ADD COLUMN chapter_order INT',
                $this->tableName
            );

            $connection->exec($query);
        } catch (DriverException $ex) {
        }
        try {
            $query = sprintf(
                'ALTER TABLE `%s` ADD COLUMN is_additional_data TINYINT(1) NOT NULL DEFAULT 0',
                $this->tableName
            );

            $connection->exec($query);
        } catch (DriverException $ex) {
        }
        try {
            $query = sprintf(
                'ALTER TABLE `%s` ADD COLUMN serialized_name VARCHAR(255)',
                $this->tableName
            );

            $connection->exec($query);
        } catch (DriverException $ex) {
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
