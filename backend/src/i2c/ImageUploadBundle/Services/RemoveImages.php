<?php

namespace i2c\ImageUploadBundle\Services;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\DBAL\Driver\Connection;
use Symfony\Component\Finder\Finder;

/**
 * Class RemoveImages
 *
 * @package i2c\ImageUploadBundle\Services
 */
class RemoveImages
{
    /** @var Connection  */
    protected $connection;

    /** @var string */
    protected $imagesDir;

    /**
     * RemoveImages constructor.
     *
     * @param Registry $registry
     * @param string   $imagesDir
     */
    public function __construct(Registry $registry, $imagesDir)
    {
        $this->connection = $registry->getEntityManager()->getConnection();
        $this->imagesDir = $imagesDir;
    }

    /**
     * @return int
     */
    public function removeExtraImages()
    {
        $finder = new Finder();

        $finder->directories()->in($this->imagesDir);
        $references = $this->getUploadedImagesReferences();

        $pattern = "/<img.+?src=.+?[\"'](.+?)[\"'].*?>/";
        foreach ($references as $reference) {
            $content = $this->getChapterContent($reference['chapter_id']);
            preg_match_all($pattern, $content, $match);
            //TODO check $match array values
        }

        /* TODO remove the rest of existing images
           TODO the matched files can be excluded
        foreach ($finder as $file) {
            $path = $file->getRelativePathName();
            if (strpos($path, DIRECTORY_SEPARATOR) !== false) {
                $evalIds = explode(DIRECTORY_SEPARATOR, $path);
            }
        }
        */

        return 0;
    }

    /**
     * @param $cid
     *
     * @return mixed
     */
    protected function getChapterContent($cid)
    {
        $query = sprintf(
            'SELECT content AS content
             FROM i2c_chapter
             WHERE id = %s',
            $cid
        );

        return $this->connection->fetchColumn($query);
    }

    /**
     * @return array
     */
    protected function getUploadedImagesReferences()
    {
        $query = 'SELECT evaluation_id, chapter_id
                  FROM i2c_images_queue';

        return $this->connection->fetchAll($query);
    }
}

