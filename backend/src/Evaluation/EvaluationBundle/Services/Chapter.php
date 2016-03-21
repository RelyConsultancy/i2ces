<?php

namespace Evaluation\EvaluationBundle\Services;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Evaluation\EvaluationBundle\Entity\Chapter as ChapterEntity;
use Evaluation\EvaluationBundle\Exception\FormException;
use JMS\Serializer\Serializer;

/**
 * Class Chapter
 *
 * @package Evaluation\EvaluationBundle\Services
 */
class Chapter
{
    const STATE_VISIBLE = 'visible';
    const STATE_HIDDEN = 'hidden';

    /** @var Serializer  */
    protected $serializer;

    protected $entityManager;

    /**
     * Chapter constructor.
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
     * @param ChapterEntity $chapter
     * @param ChapterEntity $sentChapter
     *
     * @return ChapterEntity
     * @throws FormException
     */
    public function getUpdateChapter(ChapterEntity $chapter, ChapterEntity $sentChapter)
    {
        $chapter->setContent($sentChapter->getContent());

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
     * @param ChapterEntity $chapter
     *
     * @return array
     */
    protected function getChapterErrors(ChapterEntity $chapter)
    {
        $errors = [];

        if (is_null($chapter->getTitle())) {
            $errors['chapter.title'] = 'Title must not be null';
        }

        if (self::STATE_VISIBLE !== $chapter->getState() && self::STATE_HIDDEN !== $chapter->getState()) {
            $errors['chapter.state'] = 'Invalid chapter state';
        }

        return $errors;
    }

    /**
     * @param ChapterEntity $chapter
     * @param string        $serializedUpdatedChapter
     *
     * @return ChapterEntity
     * @throws FormException
     */
    public function updateChapter(ChapterEntity $chapter, $serializedUpdatedChapter)
    {
        /** @var ChapterEntity $sentChapter */
        $sentChapter = $this->serializer->deserialize(
            $serializedUpdatedChapter,
            'Evaluation\EvaluationBundle\Entity\Chapter',
            'json'
        );

        $chapter = $this->getUpdateChapter($chapter, $sentChapter);

        $chapter->getContent();

        $this->entityManager->persist($chapter);

        $this->entityManager->flush();

        $this->entityManager->refresh($chapter);

        return $chapter;
    }
}
