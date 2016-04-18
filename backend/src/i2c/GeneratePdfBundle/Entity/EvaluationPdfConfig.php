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

    protected $delay;

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

    /**
     * @return mixed
     */
    public function getDelay()
    {
        return $this->delay;
    }

    /**
     * @param mixed $delay
     */
    public function setDelay($delay)
    {
        $this->delay = $delay;
    }
}
