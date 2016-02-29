<?php

namespace Evaluation\EvaluationBundle\Services;

use Evaluation\SetupBundle\Services\AbstractSchemaUpdateService;

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
            "CREATE TABLE IF NOT EXISTS `%s` (
                id INT(11) NOT NULL AUTO_INCREMENT,
                uid VARCHAR(255) NOT NULL,
                display_name VARCHAR(255) NOT NULL,
                start_date DATETIME,
                end_date DATETIME,
                evaluation_generation_date DATETIME,
                business_unit_id INT(11),
                FOREIGN KEY (business_unit_id) REFERENCES oro_business_unit (id),
                PRIMARY KEY (id)
            )",
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
    }
}
