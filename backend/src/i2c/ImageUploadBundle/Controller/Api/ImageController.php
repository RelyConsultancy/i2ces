<?php

namespace i2c\ImageUploadBundle\Controller\Api;

use FOS\RestBundle\View\View;
use i2c\EvaluationBundle\Controller\Api\RestApiController;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;

/**
 * Class ImageController
 *
 * @package i2c\ImageUploadBundle\Controller\Api
 */
class ImageController extends RestApiController
{
    /**
     * Upload an image and return its info.
     *
     * @param string $evaluationId
     * @param string $chapterId
     *
     * @return View
     */
    public function uploadImageAction($evaluationId, $chapterId)
    {
        try {
            $request = $this->getRequest();
            $fileUploader = $this->get('i2c_image_upload.image_upload');

            $image = $fileUploader->process($request->files->get('image'), $evaluationId, $chapterId);

            $imagePath = $this->getParameter('evaluation_frontend_image_path').$image;

            $imageInfo = $fileUploader->getImageInfo($imagePath);
        } catch (UploadException $e) {
            return $this->clientFailure($e->getMessage());
        } catch (IOException $e) {
            return $this->serverFailure($e->getMessage());
        } catch (FileException $e) {
            return $this->serverFailure($e->getMessage());
        }

        return $this->success($imageInfo);
    }
}
