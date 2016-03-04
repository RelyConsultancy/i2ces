<?php

namespace Evaluation\SetupBundle\Services;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class SchemaUpdateContainer
 *
 * @package Evaluation\SetupBundle\Services
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
     * @param AbstractSchemaUpdateService $schemaUpdateService
     */
    public function addSchemaUpdateService(AbstractSchemaUpdateService $schemaUpdateService)
    {
        if (!$this->schemaUpdateServices->contains($schemaUpdateService)) {
            $this->schemaUpdateServices->add($schemaUpdateService);
        }
    }
}
