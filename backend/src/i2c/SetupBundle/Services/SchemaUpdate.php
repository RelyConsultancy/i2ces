<?php

namespace i2c\SetupBundle\Services;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Exception;
use Symfony\Component\Filesystem\Filesystem;
use Doctrine\DBAL\Connection;

/**
 * Class SchemaUpdate
 *
 * @package i2c\SetupBundle\Services
 */
class SchemaUpdate
{
    /** @var EntityManager  */
    protected $connection;

    /** @var string */
    protected $migrationDir;

    /**
     * SchemaUpdate constructor.
     *
     * @param Connection $connection
     * @param string   $migrationDir
     */
    public function __construct(Connection $connection, $migrationDir)
    {
        $this->connection = $connection;
        $this->migrationDir = $migrationDir;
    }

    /**
     * Load sql data from a given version and execute it.
     *
     * @param string $version
     * @param string $migrationFile
     *
     * @return int
     *
     * @throws Exception
     * @throws \Doctrine\DBAL\DBALException
     */
    public function update($version, $migrationFile)
    {
        $fs = new Filesystem();
        $filePath = $this->migrationDir.$version.'/'.$migrationFile;

        if (!$fs->exists($filePath)) {
            throw new Exception(
                sprintf('There is no migration file for version \'%s\'.', $version)
            );
        }

        $sqlContent = file_get_contents($filePath);
        $this->connection->exec($sqlContent);

        return $version;
    }
}
