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

    public function startImport($importData)
    {
        // create row
    }

    public function endImport($importId, $lastImportedFolder)
    {
        //update the row
    }
}
