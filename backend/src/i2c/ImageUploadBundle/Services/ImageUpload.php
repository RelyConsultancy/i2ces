<?php

namespace i2c\ImageUploadBundle\Services;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class ImageUpload
 *
 * @package i2c\ImageUploadBundle\Services
 */
class ImageUpload
{
    const IMAGE_WIDTH_SIZE_INDEX = 0;
    const IMAGE_HEIGHT_SIZE_INDEX = 1;

    /**
     * Saves the 'image' in the folder specified at 'folderPath' and returns the moved file
     *
     * @param UploadedFile $image
     * @param string       $folderPath The absolute path to the folder where to save the image
     *
     * @return File
     *
     * @throws FileException
     * @throws UploadException
     */
    public function saveImageToDisk($image, $folderPath)
    {
        $fs = new Filesystem();

        if (!is_object($image)) {
            throw new UploadException('No valid file found.');
        }

        if ($image->getError()) {
            throw new UploadException($image->getErrorMessage());
        }

        if (!$fs->exists($folderPath)) {
            $fs->mkdir($folderPath, 0755);
        }

        $movedImage = $image->move($folderPath, $image->getClientOriginalName());

        return $movedImage;
    }

    /**
     * Returns an array with image width and height.
     *
     * @param string $imagePath
     *
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
            'link'   => $imagePath,
            'width'  => $imageInfo[self::IMAGE_WIDTH_SIZE_INDEX],
            'height' => $imageInfo[self::IMAGE_HEIGHT_SIZE_INDEX],
        );
    }
}
