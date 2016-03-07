<?php

namespace Evaluation\FileUploadBundle\Controller\Api\Rest;

use FOS\RestBundle\View\View;
use i2c\EvaluationBundle\Controller\RestApiController;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;

/**
 * Class FileController
 *
 * @package Evaluation\FileUploadBundle\Controller\Api\Rest
 */
class FileController extends RestApiController
{
    /**
     * Upload an image and return its path.
     *
     * @param string $evaluationId
     *
     * @return View
     */
    public function uploadImageAction($evaluationId)
    {
        try {
            $request = $this->getRequest();
            $fileUploader = $this->get('evaluation_file_upload.file_upload');
            $image = $fileUploader->process($request->files->get('file'), $evaluationId);
            $imageUrl = $request->getUriForPath($this->getParameter('evaluation_frontend_image_path').$image);
        } catch (UploadException $e) {
            return $this->clientFailure($e->getMessage());
        } catch (IOException $e) {
            return $this->serverFailure($e->getMessage());
        } catch (FileException $e) {
            return $this->serverFailure($e->getMessage());
        }

        return $this->success($imageUrl);
    }
}
