<?php

namespace Evaluation\EvaluationBundle\Services;

use Evaluation\SetupBundle\Services\AbstractSchemaUpdateService;

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
                id INT(11) NOT NULL,
                uid VARCHAR(255) NOT NULL,
                title VARCHAR(255) NOT NULL,
                state VARCHAR(255) NOT NULL,
                location VARCHAR(255) NOT NULL,
                content BLOB,
                created_at DATETIME,
                last_modified_at DATETIME,
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
    }
}
