<?php

namespace i2c\EvaluationBundle\Services;

use Doctrine\ORM\EntityManager;
use i2c\EvaluationBundle\Entity\Chapter as ChapterEntity;
use i2c\EvaluationBundle\Exception\FormException;
use JMS\Serializer\Serializer;

/**
 * Class Chapter
 *
 * @package i2c\EvaluationBundle\Services
 */
class Chapter
{
    const STATE_VISIBLE = 'visible';
    const STATE_HIDDEN = 'hidden';

    /** @var Serializer */
    protected $serializer;

    protected $entityManager;

    /**
     * Chapter constructor.
     *
     * @param Serializer    $serializer
     * @param EntityManager $entityManager
     */
    public function __construct(Serializer $serializer, EntityManager $entityManager)
    {
        $this->serializer = $serializer;

        $this->entityManager = $entityManager;
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
        if ($message = $this->validateChapterContent($chapter->getContent())) {
            $errors['chapter.content'] = $message;
        }

        if (is_null($chapter->getTitle())) {
            $errors['chapter.title'] = 'Title must not be null';
        }

        if (self::STATE_VISIBLE !== $chapter->getState() && self::STATE_HIDDEN !== $chapter->getState()) {
            $errors['chapter.state'] = 'Invalid chapter state';
        }

        return $errors;
    }

    /**
     * Validate chapter content
     *
     * @param $chapterContent
     *
     * @return bool|string
     */
    protected function validateChapterContent($chapterContent)
    {
        $result = false;
        if (empty($chapterContent)) {
            return 'Content must not be null';
        }

        json_decode($chapterContent);
        if (json_last_error()) {
            $result = 'Content has an invalid format.';
        }

        return $result;
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
            'i2c\EvaluationBundle\Entity\Chapter',
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
