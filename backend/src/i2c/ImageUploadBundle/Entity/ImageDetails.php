<?php

namespace i2c\ImageUploadBundle\Entity;

/**
 * Class ImageDetails
 *
 * @package i2c\ImageUploadBundle\Entity
 */
class ImageDetails
{
    /** @var  string */
    protected $width;

    /** @var  string */
    protected $height;

    /**
     * @return string
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param string $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * @return string
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param string $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }
}
