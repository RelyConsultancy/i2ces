<?php

namespace Evaluation\EvaluationBundle\Services;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Evaluation\EvaluationBundle\Entity\Chapter;
use Evaluation\UtilBundle\Exception\FormException;
use Evaluation\UtilBundle\Helpers\ChapterHelper;
use JMS\Serializer\Serializer;

/**
 * Class ChapterService
 *
 * @package Evaluation\EvaluationBundle\Services
 */
class ChapterService
{
    /** @var Serializer  */
    protected $serializer;

    protected $entityManager;

    /**
     * ChapterService constructor.
     *
     * @param Serializer $serializer
     * @param Registry   $registry
     */
    public function __construct(Serializer $serializer, Registry $registry)
    {
        $this->serializer = $serializer;

        $this->entityManager = $registry->getEntityManager();
    }

    /**
     * @param Chapter $chapter
     * @param Chapter $sentChapter
     *
     * @return Chapter
     * @throws FormException
     */
    public function getUpdateChapter(Chapter $chapter, Chapter $sentChapter)
    {
        $chapter->setContent(json_encode($sentChapter->getContent()));

        $chapter->setLastModifiedAt(new \DateTime('now'));

        $chapter->setTitle($sentChapter->getTitle());

        $chapter->setState($sentChapter->getState());

        $errors = $this->getChapterErrors($chapter);

        if (count($errors) > 0) {
            throw new FormException($errors);
        }

        return $chapter;
    }

    /**
     * @param Chapter $chapter
     *
     * @return array
     */
    protected function getChapterErrors(Chapter $chapter)
    {
        $errors = [];

        if (is_null($chapter->getTitle())) {
            $errors['chapter.title'] = 'Title must not be null';
        }

        if (!isset(ChapterHelper::$states[$chapter->getState()])) {
            $errors['chapter.state'] = 'Invalid chapter state';
        }

        return $errors;
    }

    /**
     * @param Chapter $chapter
     * @param         $serializedUpdatedChapter
     *
     * @return Chapter
     * @throws FormException
     */
    public function updateChapter(Chapter $chapter, $serializedUpdatedChapter)
    {
        /** @var Chapter $sentChapter */
        $sentChapter = $this->serializer->deserialize(
            $serializedUpdatedChapter,
            'Evaluation\EvaluationBundle\Entity\Chapter',
            'json'
        );

        $chapter = $this->getUpdateChapter($chapter, $sentChapter);

        $this->entityManager->persist($chapter);

        $this->entityManager->flush();

        $this->entityManager->refresh($chapter);

        return $chapter;
    }
}
