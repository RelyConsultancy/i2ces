<?php

namespace i2c\FileUploadBundle\Services;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;

/**
 * Class FileManager
 *
 * @package i2c\FileUploadBundle\Services
 */
class FileManager
{
    /** @var EntityManager */
    protected $entityManager;

    /**
     * FileManager constructor.
     *
     * @param Registry $registry
     */
    public function __construct(Registry $registry)
    {
        $this->entityManager = $registry->getEntityManager();
    }

    /**
     * @param string $evalId
     * @param string $chapterId
     * @param array  $images
     *
     * @return int
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function updateFileReferences($evalId, $chapterId, $images)
    {
        $this->clearExistingReferences($evalId, $chapterId);

        $conn = $this->entityManager->getConnection();

        $insertValues = array();
        foreach ($images as $image) {
            $insertValues[] = sprintf(
                '(\'%s\',\'%s\',\'%s\')',
                $evalId,
                $chapterId,
                $image
            );
        }

        $query = sprintf(
            'INSERT INTO i2c_chapter_updates_queue (evaluation_id, chapter_id, image_path)
             VALUES %s',
            $evalId,
            $chapterId,
            implode(',', $insertValues)
        );

        return $conn->exec($query);
    }

    /**
     * @param string $evalId
     * @param string $chapterId
     *
     * @return int
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function clearExistingReferences($evalId, $chapterId)
    {
        $conn = $this->entityManager->getConnection();
        $query = sprintf(
            'DELETE FROM i2c_chapter_updates_queue
             WHERE evaluation_id=\'%s\'
              AND chapter_id=\'%s\'',
            $evalId,
            $chapterId
        );

        return $conn->exec($query);
    }
}
