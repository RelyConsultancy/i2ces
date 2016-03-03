<?php

namespace Evaluation\FileUploadBundle\Controller\Api\Rest;

use FOS\RestBundle\View\View;
use Oro\Bundle\ApiBundle\Controller\RestApiController;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;

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
        $request = $this->getRequest();
        $fileUploader = $this->get('evaluation_file_upload.file_upload');

        try {
            $imagePath = $fileUploader->process($request->files->get('file'), $evaluationId);
        } catch (AccessDeniedException $e) {
            $imagePath = false;
        }

        //@TODO Change after the merge of IES-55 branch.

        return new JsonResponse($imagePath);
    }
}
