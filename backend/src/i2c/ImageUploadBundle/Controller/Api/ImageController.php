<?php

namespace i2c\ImageUploadBundle\Controller\Api;

use FOS\RestBundle\View\View;
use i2c\EvaluationBundle\Controller\Api\RestApiController;
use i2c\EvaluationBundle\Services\EvaluationDataBaseManager;
use i2c\ImageUploadBundle\Services\ImageProcessing;
use i2c\ImageUploadBundle\Services\ImageUpload;
use i2c\PageBundle\Services\PageDatabaseManager;
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
                return $this->notFound(sprintf('Evaluation with ID %s was not found', $evaluationId));
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

            $imageInfo = $this->getImageProcessingService()->getImageDetails($image->getRealPath());

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
                    'width' => $imageInfo->getWidth(),
                    'height' => $imageInfo->getHeight(),
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
     * Upload an image for a specific page.
     *
     * @param string $type
     *
     * @return View
     *
     * @Acl(
     *     id="i2c_page_edit",
     *     type="entity",
     *     class="i2cPageBundle:Page",
     *     permission="EDIT"
     * )
     */
    public function uploadPageImageAction($type)
    {
        try {
            $page = $this->getPageDatabaseManager()->getPageForEditing($type);

            if (is_null($page)) {
                return $this->notFound(sprintf('%s page was not found', $type));
            }

            $request = $this->getRequest();

            $imageUploadService = $this->getImageUploadService();

            $imageUploadPath = $this->getParameter('upload_image_path');

            $pageImagesDirectory = $this->getParameter('pages_image_upload_directory');

            $path = sprintf(
                '%s/%s/%s',
                $imageUploadPath,
                $pageImagesDirectory,
                $type
            );

            $image = $imageUploadService->saveImageToDisk($request->files->get('image'), $path);

            $imageInfo = $this->getImageProcessingService()->getImageDetails($image->getRealPath());

            return $this->success(
                [
                    'link'  => sprintf(
                        '/%s/%s/%s/%s',
                        'images',
                        $pageImagesDirectory,
                        $type,
                        $image->getFilename()
                    ),
                    'width' => $imageInfo->getWidth(),
                    'height' => $imageInfo->getHeight(),
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

    /**
     * @return PageDatabaseManager
     */
    protected function getPageDatabaseManager()
    {
        return $this->get('i2c_page.page_database_manager_service');
    }

    /**
     * @return ImageProcessing
     */
    protected function getImageProcessingService()
    {
        return $this->get('i2c_image_upload.image_processing');
    }
}
