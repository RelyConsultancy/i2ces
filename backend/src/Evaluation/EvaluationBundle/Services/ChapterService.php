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
        $errors = [];

        $chapter->setContent(json_encode($sentChapter->getContent()));

        $chapter->setLastModifiedAt(new \DateTime('now'));

        if (!is_null($sentChapter->getTitle())) {
            $chapter->setTitle($sentChapter->getTitle());
        } else {
            $errors['chapter.title'] = "Title must not be null";
        }

        if (isset(ChapterHelper::$states[$sentChapter->getState()])) {
            $chapter->setState($sentChapter->getState());
        } else {
            $errors['chapter.state'] = "Invalid chapter state";
        }

        if (count($errors) > 0) {
            throw new FormException($errors);
        }

        return $chapter;
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
