<?php

namespace i2c\GenerateEvaluationBundle\Entity;

/**
 * Class ImportOption
 *
 * @package i2c\GenerateEvaluationBundle\Entity
 */
class ImportOption
{
    /** @var string */
    protected $importFilePath;

    /** @var string */
    protected $fieldSeparator;

    /** @var string */
    protected $lineEndings;

    /**
     * InputOption constructor.
     *
     * @param string $importFilePath
     * @param string $fieldSeparator
     * @param string $lineEndings
     */
    public function __construct($importFilePath, $fieldSeparator, $lineEndings)
    {
        $this->importFilePath = $importFilePath;
        $this->fieldSeparator = $fieldSeparator;
        $this->lineEndings = $lineEndings;
    }

    /**
     * @return mixed
     */
    public function getImportFilePath()
    {
        return $this->importFilePath;
    }

    /**
     * @param mixed $importFilePath
     */
    public function setImportFilePath($importFilePath)
    {
        $this->importFilePath = $importFilePath;
    }

    /**
     * @return mixed
     */
    public function getFieldSeparator()
    {
        return $this->fieldSeparator;
    }

    /**
     * @param mixed $fieldSeparator
     */
    public function setFieldSeparator($fieldSeparator)
    {
        $this->fieldSeparator = $fieldSeparator;
    }

    /**
     * @return mixed
     */
    public function getLineEndings()
    {
        return $this->lineEndings;
    }

    /**
     * @param mixed $lineEndings
     */
    public function setLineEndings($lineEndings)
    {
        $this->lineEndings = $lineEndings;
    }
}
