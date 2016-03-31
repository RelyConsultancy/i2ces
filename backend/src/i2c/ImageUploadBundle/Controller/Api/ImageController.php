<?php

namespace i2c\ImageUploadBundle\Controller\Api;

use FOS\RestBundle\View\View;
use i2c\EvaluationBundle\Controller\Api\RestApiController;
use i2c\EvaluationBundle\Services\EvaluationDataBaseManager;
use i2c\ImageUploadBundle\Services\ImageUpload;
use Oro\Bundle\SecurityBundle\Annotation\Acl;
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
     * @Acl(
     *     id="evaluation_chapter_edit",
     *     type="entity",
     *     class="i2cEvaluationBundle:Evaluation",
     *     permission="EDIT"
     * )
     *
     * @return View
     */
    public function uploadImageAction($evaluationId, $chapterId)
    {
        try {
            $evaluation = $this->getEvaluationDatabaseManager()->getByCidForEditing($evaluationId);

            if (is_null($evaluation)) {
                return $this->notFound('Evaluation not found');
            }

            $request = $this->getRequest();

            $imageUploadService = $this->getImageUploadService();

            $imageUploadPath = $this->getParameter('upload_image_path');

            $evaluationImagesDirectory = $this->getParameter('evaluation_image_upload_directory');

            $path = sprintf(
                '%s/%s/%s/%s',
                $imageUploadPath,
                $evaluationImagesDirectory,
                $evaluationId,
                $chapterId
            );

            $image = $imageUploadService->saveImageToDisk($request->files->get('image'), $path);

            $imageInfo = getimagesize($image->getRealPath());

            return $this->success(
                [
                    'link'  => sprintf(
                        '/%s/%s/%s/%s/%s',
                        'images',
                        $evaluationImagesDirectory,
                        $evaluationId,
                        $chapterId,
                        $image->getFilename()
                    ),
                    'width' => $imageInfo[0],
                    'height' => $imageInfo[1],
                ]
            );

        } catch (UploadException $e) {
            return $this->clientFailure("There was an error while uploading the image", $e->getMessage());
        } catch (IOException $e) {
            return $this->serverFailure($e->getMessage());
        } catch (FileException $e) {
            return $this->serverFailure($e->getMessage());
        }
    }

    /**
     * @return ImageUpload
     */
    protected function getImageUploadService()
    {
        return $this->get('i2c_image_upload.image_upload');
    }

    /**
     * @return EvaluationDataBaseManager
     */
    protected function getEvaluationDatabaseManager()
    {
        return $this->get('i2c_evaluation.evaluation_database_manager_service');
    }
}
