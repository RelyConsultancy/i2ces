<?php

namespace i2c\GenerateEvaluationBundle\Services;

use i2c\GenerateEvaluationBundle\Entity\ImportOption;

/**
 * Class ImportData
 *
 * @package i2c\GenerateEvaluationBundle\Services
 */
class ImportData
{
    /** @var \PDO */
    protected $pdoConnection;

    /**
     * Build and set PDO connection.
     *
     * @param string $dbhost
     * @param string $dbuser
     * @param string $dbpass
     * @param string $dbname
     */
    public function buildPdoConnection($dbhost, $dbuser, $dbpass, $dbname)
    {
        if (!$this->pdoConnection) {
            $this->pdoConnection = new \PDO(
                'mysql:host=' . $dbhost . ';dbname=' . $dbname,
                $dbuser,
                $dbpass,
                array(
                    \PDO::MYSQL_ATTR_LOCAL_INFILE => true
                )
            );
        }
    }

    /**
     * Import campaign data.
     *
     * @param ImportOption $inputOptions
     * @param array        $rawTablesConfig
     */
    public function import(ImportOption $inputOptions, $rawTablesConfig)
    {
        foreach ($rawTablesConfig as $key => $value) {
            if ($this->createTable($key, $value) === false) {
                throw new \PDOException('Unable to create table: '.$key);
            }

            $this->loadDataFromFile(
                $key,
                array_keys($value['columns']),
                sprintf('%s/%s', $inputOptions->getImportFilePath(), $value['file_name']),
                $inputOptions->getFieldSeparator(),
                $inputOptions->getLineEndings(),
                (!empty($value['additional_setters'])) ? $value['additional_setters'] : ''
            );
        }
    }

    /**
     * Load data from import files and save it in database.
     *
     * @param string       $tableName
     * @param string       $filePath
     * @param array        $columns
     * @param string       $fieldSeparator
     * @param string       $lineEndings
     * @param string|array $additionalSetters
     *
     * @return int
     */
    protected function loadDataFromFile(
        $tableName,
        $columns,
        $filePath,
        $fieldSeparator,
        $lineEndings,
        $additionalSetters
    ) {
        if ($additionalSetters) {
            $additionalSetters = sprintf(
                'SET %s',
                implode(',', $additionalSetters)
            );
        }

        $query = sprintf(
            "LOAD DATA LOCAL INFILE '%s' INTO TABLE `%s`
            FIELDS TERMINATED BY '%s'
            ENCLOSED BY '\"'
            LINES TERMINATED BY '%s'
            IGNORE 1 lines
            (%s)
            %s",
            $filePath,
            $tableName,
            $fieldSeparator,
            $lineEndings,
            implode(',', $columns),
            $additionalSetters
        );

        return $this->pdoConnection->exec($query);
    }

    /**
     * Create a table.
     * Returns false in case of mysql error or the number of affected rows
     * (0 in case table exists).
     *
     * @param string $tableName
     * @param array  $config
     *
     * @return bool|int
     */
    protected function createTable($tableName, $config)
    {
        $query = sprintf(
            'CREATE TABLE IF NOT EXISTS `%s` (`id` INT(11) NOT NULL AUTO_INCREMENT, ',
            $tableName
        );

        $tableColumns = [];
        foreach ($config['columns'] as $fieldName => $fieldConfig) {
            $tableColumns[] = sprintf(
                '`%s` %s',
                str_replace('@', '', $fieldName),
                $fieldConfig
            );
        }
        $query = sprintf(
            '%s %s, PRIMARY KEY(`id`))',
            $query,
            implode(',', $tableColumns)
        );

        return $this->pdoConnection->exec($query);
    }
}
