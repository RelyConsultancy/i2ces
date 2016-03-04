<?php

namespace Evaluation\SetupBundle\Services;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class AbstractSchemaUpdateService
 *
 * Abstract schema update service for manual schema control
 *
 * @package Evaluation\SetupBundle\Services
 */
abstract class AbstractSchemaUpdateService
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var  string */
    protected $tableName;

    /**
     * AbstractSchemaUpdateService constructor.
     *
     * @param Registry $doctrine
     * @param string   $tableName
     */
    public function __construct(Registry $doctrine, $tableName)
    {
        $this->entityManager = $doctrine->getEntityManager();
        $this->tableName = $tableName;
    }

    /**
     * Creates the table if it does not exist and updates the schema for it
     */
    public function updateSchema()
    {
        $this->createTable();

        $this->updateTable();
    }

    /**
     * Returns the table which the schema update service alters
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * Creates the table if it doesn't exist
     */
    abstract protected function createTable();

    /**
     * Updates the table schema
     */
    abstract protected function updateTable();
}
