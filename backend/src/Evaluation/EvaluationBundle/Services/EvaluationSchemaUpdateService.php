<?php

namespace Evaluation\EvaluationBundle\Services;

use Doctrine\DBAL\Exception\DriverException;
use Doctrine\DBAL\Exception\InvalidFieldNameException;
use i2c\SetupBundle\Services\AbstractSchemaUpdateService;

/**
 * Class EvaluationSchemaUpdateService
 *
 * @package Evaluation\EvaluationBundle\Services
 */
class EvaluationSchemaUpdateService extends AbstractSchemaUpdateService
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
                cid VARCHAR(255) NOT NULL,
                title VARCHAR(255) NOT NULL,
                category VARCHAR(255) NOT NULL,
                brand VARCHAR(255) NOT NULL,
                state VARCHAR(255) NOT NULL,
                start_date DATETIME,
                end_date DATETIME,
                generated_at DATETIME,
                business_unit_id INT(11),
                FOREIGN KEY (business_unit_id) REFERENCES oro_business_unit (id),
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
                'ALTER TABLE `%s` CHANGE evaluation_generation_date generated_at DATETIME',
                $this->tableName
            );

            $connection->exec($query);
        } catch (InvalidFieldNameException $ex) {
        }
        try {
            $query = sprintf(
                'ALTER TABLE `%s` CHANGE display_name title VARCHAR(255) NOT NULL',
                $this->tableName
            );

            $connection->exec($query);
        } catch (InvalidFieldNameException $ex) {
        }
        try {
            $query = sprintf(
                'ALTER TABLE `%s` ADD COLUMN category VARCHAR(255) NOT NULL',
                $this->tableName
            );

            $connection->exec($query);
        } catch (DriverException $ex) {
        }
        try {
            $query = sprintf(
                'ALTER TABLE `%s` ADD COLUMN brand VARCHAR(255) NOT NULL',
                $this->tableName
            );

            $connection->exec($query);
        } catch (DriverException $ex) {
        }
        try {
            $query = sprintf(
                'ALTER TABLE `%s` ADD COLUMN state VARCHAR(255) NOT NULL',
                $this->tableName
            );

            $connection->exec($query);
        } catch (DriverException $ex) {
        }
        try {
            $query = sprintf(
                'ALTER TABLE `%s` ADD COLUMN cid VARCHAR(255) NOT NULL',
                $this->tableName
            );

            $connection->exec($query);
        } catch (DriverException $ex) {
        }
        try {
            $query = ('DROP TABLE IF EXISTS `medium`');
            $connection->exec($query);
        } catch (DriverException $ex) {
        }
        try {
            $query = ('DROP TABLE IF EXISTS `evaluation_mediums`');
            $connection->exec($query);
        } catch (DriverException $ex) {
        }
    }
}
