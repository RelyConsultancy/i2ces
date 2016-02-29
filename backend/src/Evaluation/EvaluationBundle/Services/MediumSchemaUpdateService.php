<?php

namespace Evaluation\EvaluationBundle\Services;

use Evaluation\SetupBundle\Services\AbstractSchemaUpdateService;

class MediumSchemaUpdateService extends AbstractSchemaUpdateService
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
                medium_name VARCHAR(255) NOT NULL,
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
