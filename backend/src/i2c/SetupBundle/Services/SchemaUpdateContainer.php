<?php

namespace i2c\SetupBundle\Services;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class SchemaUpdateContainer
 *
 * @package i2c\SetupBundle\Services
 */
class SchemaUpdateContainer
{
    /** @var ArrayCollection  */
    protected $schemaUpdateServices;

    /**
     * SchemaUpdateContainer constructor.
     */
    public function __construct()
    {
        $this->schemaUpdateServices = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getSchemaUpdateServices()
    {
        return $this->schemaUpdateServices;
    }

    /**
     * Adds a SchemaUpdateService to the container
     *
     * @param AbstractSchemaUpdate $schemaUpdateService
     */
    public function addSchemaUpdateService(AbstractSchemaUpdate $schemaUpdateService)
    {
        if (!$this->schemaUpdateServices->contains($schemaUpdateService)) {
            $this->schemaUpdateServices->add($schemaUpdateService);
        }
    }
}
