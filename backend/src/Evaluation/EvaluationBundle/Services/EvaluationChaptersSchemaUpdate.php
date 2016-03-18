<?php

namespace Evaluation\EvaluationBundle\Services;

use i2c\SetupBundle\Services\AbstractSchemaUpdate;

/**
 * Class EvaluationChaptersSchemaUpdate
 *
 * @package Evaluation\EvaluationBundle\Services
 */
class EvaluationChaptersSchemaUpdate extends AbstractSchemaUpdate
{
    /**
     * Creates the table if it does not exist
     */
    protected function createTable()
    {
        $query = sprintf(
            'CREATE TABLE IF NOT EXISTS `%s` (
                evaluation_id INT(11) NOT NULL,
                chapter_id INT(11) NOT NULL,
                FOREIGN KEY (evaluation_id) REFERENCES evaluation(id),
                FOREIGN KEY (chapter_id) REFERENCES chapter(id),
                PRIMARY KEY (evaluation_id, chapter_id)
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
    }
}
