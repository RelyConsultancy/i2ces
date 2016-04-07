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

    /** @var  string */
    protected $databaseName;

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
                'mysql:host='.$dbhost.';dbname='.$dbname,
                $dbuser,
                $dbpass,
                array(
                    \PDO::MYSQL_ATTR_LOCAL_INFILE => true,
                )
            );
        }
        $this->databaseName = $dbname;
    }

    /**
     * Import campaign data.
     *
     * @param ImportOption $inputOptions
     * @param array        $rawTablesConfig
     */
    public function import(ImportOption $inputOptions, $rawTablesConfig)
    {
        $this->removeExistingCampaignsThatAreImported($inputOptions, $rawTablesConfig);

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
     * @param ImportOption $importOption
     * @param array        $rawTablesConfig
     */
    protected function removeExistingCampaignsThatAreImported(ImportOption $importOption, $rawTablesConfig)
    {
        if (!$this->tableExists($rawTablesConfig['ie_campaign_data']['table_name'])) {
            return;
        }

        $this->createTable('temp_campaign_ids', $rawTablesConfig['ie_campaign_data']);

        $this->loadDataFromFile(
            'temp_campaign_ids',
            array_keys($rawTablesConfig['ie_campaign_data']['columns']),
            sprintf(
                '%s/%s',
                $importOption->getImportFilePath(),
                $rawTablesConfig['ie_campaign_data']['file_name']
            ),
            $importOption->getFieldSeparator(),
            $importOption->getLineEndings(),
            ''
        );

        $cids = $this->pdoConnection->query(
            'SELECT master_campaign_id FROM temp_campaign_ids'
        )->fetchAll(\PDO::FETCH_COLUMN);

        $cidsString = sprintf('(\'%s\')', implode('\',\'', $cids));

        foreach ($rawTablesConfig as $config) {
            if (!$this->tableExists($config['table_name'])) {
                continue;
            }

            $query = sprintf(
                'DELETE FROM %s WHERE master_campaign_id in %s',
                $config['table_name'],
                $cidsString
            );

            $this->pdoConnection->exec($query);
        }
        $this->removeGeneratedEvaluations($cidsString);

        $this->pdoConnection->exec('DROP TABLE temp_campaign_ids');
    }

    /**
     * @param string $importedCids
     */
    protected function removeGeneratedEvaluations($importedCids)
    {


        $evaluationIds = $this->pdoConnection->query(
            sprintf(
                'SELECT id FROM evaluation WHERE cid IN %s',
                $importedCids
            )
        )->fetchAll(\PDO::FETCH_COLUMN);

        $evaluationIdsString = sprintf('(\'%s\')', implode('\',\'', $evaluationIds));

        $query = sprintf(
            'SELECT chapter_id FROM evaluation_chapters WHERE evaluation_id in %s',
            $evaluationIdsString
        );

        $chapterIds = $this->pdoConnection->query($query)->fetchAll(\PDO::FETCH_COLUMN);
        $chapterIdsString = sprintf('(\'%s\')', implode('\',\'', $chapterIds));

        $this->pdoConnection->exec(
            sprintf(
                'DELETE FROM evaluation_chapters where evaluation_id in %s',
                $evaluationIdsString
            )
        );

        $this->pdoConnection->exec(
            sprintf(
                'DELETE FROM evaluation where id in %s',
                $evaluationIdsString
            )
        );

        $this->pdoConnection->exec(
            sprintf(
                'DELETE FROM chapter where id in %s',
                $chapterIdsString
            )
        );
    }

    /**
     * @param string $fromTable
     */
    protected function deleteCampaignsToBeImported($fromTable, $cidColumnName)
    {
        $query = sprintf(
            ' (SELECT master_campaign_id FROM temp_campaign_ids)',
            $fromTable,
            $cidColumnName
        );

        $this->pdoConnection->exec($query);
    }

    /**
     * @param $tableName
     *
     * @return bool
     */
    protected function tableExists($tableName)
    {
        $query = sprintf(
            'SELECT *
             FROM information_schema.tables
             WHERE table_schema = \'%s\'
             AND table_name = \'%s\'
            ',
            $this->databaseName,
            $tableName
        );

        $result = $this->pdoConnection->query($query)->fetchAll();

        if (1 > count($result)) {
            return false;
        }

        return true;
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
            '%s %s, PRIMARY KEY(`id`)) %s',
            $query,
            implode(',', $tableColumns),
            'DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci'
        );

        return $this->pdoConnection->exec($query);
    }
}
