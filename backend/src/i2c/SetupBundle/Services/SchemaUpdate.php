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
    protected $entityManager;

    /** @var string */
    protected $migrationDir;

    /**
     * SchemaUpdate constructor.
     *
     * @param Registry $registry
     * @param string   $migrationDir
     */
    public function __construct(Registry $registry, $migrationDir)
    {
        $this->entityManager = $registry->getEntityManager();
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
            throw new Exception('The specified version file does not exist.');
        }

        /** @var Connection $connection */
        $connection = $this->entityManager->getConnection();

        $sqlContent = file_get_contents($filePath);

        return $connection->exec($sqlContent);
    }
}
