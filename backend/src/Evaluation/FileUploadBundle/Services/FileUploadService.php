<?php

namespace Evaluation\FileUploadBundle\Services;

use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
    protected $webDirPath;

    /**
     * @var string
     */
    protected $frontendPath;

    /**
     * FileUploadService constructor.
     *
     * @param string $webDirPath
     * @param string $frontendPath
     */
    public function __construct($webDirPath, $frontendPath)
    {
        $this->webDirPath = $webDirPath;
        $this->frontendPath = $frontendPath;
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
        $path = $this->webDirPath.$this->frontendPath;

        if (!is_object($file)) {
            return $result;
        }

        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }

        if (!is_writeable($path)) {
            throw new AccessDeniedException('Upload directory is not writable.');
        }

        if (!$file->getError()) {
            $dirPath = $path.$evaluationId;
            if (!is_dir($dirPath)) {
                mkdir($dirPath, 0755, true);
            }

            if ($file->move($dirPath, $file->getClientOriginalName())) {
                $result = $this->frontendPath.$evaluationId.'/'.$file->getClientOriginalName();
            }
        }

        return $result;
    }
}
