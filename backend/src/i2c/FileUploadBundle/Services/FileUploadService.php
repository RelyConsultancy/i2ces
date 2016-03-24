<?php

namespace i2c\FileUploadBundle\Services;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class FileUploadService
 *
 * @package i2c\FileUploadBundle\Services
 */
class FileUploadService
{
    const IMAGE_WIDTH_SIZE_INDEX = 0;
    const IMAGE_HEIGHT_SIZE_INDEX = 1;

    /** @var string */
    protected $uploadPath;

    /** @var string */
    protected $webPath;

    /**
     * FileUploadService constructor.
     *
     * @param string $uploadPath
     * @param string $webPath
     */
    public function __construct($uploadPath, $webPath)
    {
        $this->uploadPath = $uploadPath;
        $this->webPath = $webPath;
    }

    /**
     * Upload an image for a given evaluation.
     *
     * @param UploadedFile $file
     * @param string       $evaluationId
     *
     * @return bool|string
     */
    public function process($file, $evaluationId)
    {
        $result = false;
        $fs = new Filesystem();

        if (!is_object($file)) {
            throw new UploadException('No valid file found.');
        }

        if ($file->getError()) {
            throw new UploadException($file->getErrorMessage());
        }

        $uploadDir = $this->uploadPath.$evaluationId;
        if (!$fs->exists($uploadDir)) {
            $fs->mkdir($uploadDir, 0755);
        }

        if ($file->move($uploadDir, $file->getClientOriginalName())) {
            $result = $evaluationId.'/'.$file->getClientOriginalName();
        }

        return $result;
    }

    /**
     * Returns an array with image width and height.
     *
     * @param string $imagePath
     * @return array
     */
    public function getImageInfo($imagePath)
    {
        if (empty($imagePath)) {
            throw new FileException('No valid image found.');
        }

        $imageInfo = getimagesize($this->webPath.$imagePath);
        if (!$imageInfo) {
            throw new FileException('Unable to get image info.');
        }

        return array(
            'url'    => $imagePath,
            'width'  => $imageInfo[self::IMAGE_WIDTH_SIZE_INDEX],
            'height' => $imageInfo[self::IMAGE_HEIGHT_SIZE_INDEX]
        );
    }
}
