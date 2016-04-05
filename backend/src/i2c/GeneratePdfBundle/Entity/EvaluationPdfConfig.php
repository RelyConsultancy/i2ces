<?php

namespace i2c\GeneratePdfBundle\Entity;

/**
 * Class EvaluationPdfConfig
 *
 * @package i2c\GeneratePdfBundle\Entity
 */
class EvaluationPdfConfig
{
    protected $outputDirectory;

    protected $nodeJsCommand;

    /**
     * @return mixed
     */
    public function getOutputDirectory()
    {
        return $this->outputDirectory;
    }

    /**
     * @param mixed $outputDirectory
     */
    public function setOutputDirectory($outputDirectory)
    {
        $this->outputDirectory = $outputDirectory;
    }

    /**
     * @return mixed
     */
    public function getNodeJsCommand()
    {
        return $this->nodeJsCommand;
    }

    /**
     * @param mixed $nodeJsCommand
     */
    public function setNodeJsCommand($nodeJsCommand)
    {
        $this->nodeJsCommand = $nodeJsCommand;
    }
}
