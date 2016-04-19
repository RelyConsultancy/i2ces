<?php

namespace i2c\GenerateEvaluationBundle\Services;

/**
 * Class ImportAll
 *
 * @package i2c\GenerateEvaluationBundle\Services
 */
class ImportAll
{
    /** @var \PDO */
    protected $pdoConnection;

    /** @var string */
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
     * @param $importData
     *
     * @return mixed
     */
    public function startImport($importData)
    {
        $sql = sprintf(
            'INSERT INTO i2c_import_version (version_number, start_date, config_data)
             VALUES (%s, \'%s\', \'%s\')',
            $importData['version_number'],
            $this->getCurrentDate(),
            json_encode($importData)
        );

        $this->pdoConnection->query($sql);

        return $this->pdoConnection->lastInsertId();
    }

    /**
     * @param $importId
     * @param $lastImportedFolder
     *
     * @return int
     */
    public function endImport($importId, $lastImportedFolder)
    {
        $sql = sprintf(
            'UPDATE i2c_import_version
             SET end_date=\'%s\', last_import_folder=\'%s\'
             WHERE id=%s',
            $this->getCurrentDate(),
            $lastImportedFolder,
            $importId
        );

        return $this->pdoConnection->exec($sql);
    }

    /**
     * @param $importId
     *
     * @return int
     */
    public function markImportAsFailure($importId)
    {
        $sql = sprintf(
            'UPDATE i2c_import_version
             SET end_date=null
             WHERE id=%s',
            $this->getCurrentDate(),
            $importId
        );

        return $this->pdoConnection->exec($sql);
    }

    /**
     * @return string
     */
    public function getLastImportDate()
    {
        $sql = 'SELECT last_import_folder
                FROM i2c_import_version
                ORDER BY id DESC
                LIMIT 1';

        return $this->pdoConnection->query($sql)->fetchColumn();
    }

    /**
     * @return bool|string
     */
    protected function getCurrentDate()
    {
        $now = new \DateTime('now');

        return $now->format('Y-m-d\TH:i:s');
    }
}
