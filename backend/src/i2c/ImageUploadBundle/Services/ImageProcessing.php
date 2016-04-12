<?php

namespace i2c\ImageUploadBundle\Services;

use i2c\ImageUploadBundle\Entity\ImageDetails;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * Class ImageProcessing
 *
 * @package i2c\ImageUploadBundle\Services
 */
class ImageProcessing
{
    const IMAGE_WIDTH_INDEX = 0;
    const IMAGE_HEIGHT_INDEX = 1;

    /**
     * @param $imagePath
     * @return ImageDetails
     */
    public function getImageDetails($imagePath)
    {
        if (!$imagePath) {
            throw new FileException('No image provided.');
        }

        $details = getimagesize($imagePath);

        if ($details === false) {
            throw new FileException(
                sprintf('Could not get image details for `%s`.', $imagePath)
            );
        }

        $imageDetails = new ImageDetails();
        $imageDetails->setWidth($details[self::IMAGE_HEIGHT_INDEX]);
        $imageDetails->setHeight($details[self::IMAGE_WIDTH_INDEX]);

        return $imageDetails;
    }
}
