<?php

namespace Evaluation\FileUploadBundle\Services;

use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class FileUploadService
 *
 * @package Evaluation\FileUploadBundle\Services
 */
class FileUploadService
{
    /**
     * @var string
     */
    protected $uploadPath;

    /**
     * FileUploadService constructor.
     *
     * @param string $uploadPath
     */
    public function __construct($uploadPath)
    {
        $this->uploadPath = $uploadPath;
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
}
