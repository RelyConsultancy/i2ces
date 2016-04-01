<?php

namespace i2c\ImageUploadBundle\Services;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;

/**
 * Class UploadedImageQueue
 *
 * @package i2c\ImageUploadBundle\Services
 */
class UploadedImageQueue
{
    /** @var EntityManager */
    protected $entityManager;

    /**
     * UploadedImageQueue constructor.
     *
     * @param Registry $registry
     */
    public function __construct(Registry $registry)
    {
        $this->entityManager = $registry->getEntityManager();
    }

    /**
     * @param string $evaluationId
     * @param string $chapterId
     *
     * @return int
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function updateChapterReferences($evaluationId, $chapterId)
    {
        $conn = $this->entityManager->getConnection();

        $query = sprintf(
            'INSERT IGNORE INTO i2c_images_queue (chapter_id, evaluation_id)
             VALUES (\'%s\', \'%s\')',
            $chapterId,
            $evaluationId
        );

        return $conn->exec($query);
    }
}
