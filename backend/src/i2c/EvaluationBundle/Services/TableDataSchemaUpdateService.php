<?php

namespace i2c\EvaluationBundle\Services;

use i2c\SetupBundle\Services\AbstractSchemaUpdateService;

/**
 * Class TableDataSchemaUpdateService
 *
 * @package i2c\EvaluationBundle\Services
 */
class TableDataSchemaUpdateService extends AbstractSchemaUpdateService
{
    /**
     * Creates the table if it does not exist
     */
    protected function createTable()
    {
        $query = sprintf(
            'CREATE TABLE IF NOT EXISTS `%s` (
                id INT(11) NOT NULL AUTO_INCREMENT,
                cid VARCHAR(255),
                content BLOB,
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
