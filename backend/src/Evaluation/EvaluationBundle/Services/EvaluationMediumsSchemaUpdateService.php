<?php

namespace Evaluation\EvaluationBundle\Services;

use Evaluation\SetupBundle\Services\AbstractSchemaUpdateService;

/**
 * Class EvaluationMediumsSchemaUpdateService
 *
 * @package Evaluation\EvaluationBundle\Services
 */
class EvaluationMediumsSchemaUpdateService extends AbstractSchemaUpdateService
{
    /**
     * Creates the table if it does not exist
     */
    protected function createTable()
    {
        $query = sprintf(
            "CREATE TABLE IF NOT EXISTS `%s` (
                evaluation_id INT(11) NOT NULL,
                medium_id INT(11) NOT NULL,
                FOREIGN KEY (evaluation_id) REFERENCES evaluation(id),
                FOREIGN KEY (medium_id) REFERENCES medium(id),
                PRIMARY KEY (evaluation_id, medium_id)
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
