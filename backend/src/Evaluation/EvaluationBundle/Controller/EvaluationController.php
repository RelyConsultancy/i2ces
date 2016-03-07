<?php

namespace Evaluation\EvaluationBundle\Controller;

use Evaluation\EvaluationBundle\Entity\Evaluation;
use Evaluation\EvaluationBundle\Services\ChapterService;
use Evaluation\EvaluationBundle\Services\EvaluationDataBaseManagerService;
use Evaluation\EvaluationBundle\Services\EvaluationService;
use Evaluation\UtilBundle\Exception\FormException;
use i2c\EvaluationBundle\Controller\RestApiController;
use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class EvaluationController
 *
 * @package Evaluation\EvaluationBundle\Controller
 */
class EvaluationController extends RestApiController
{
    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Acl(
     *     id="evaluation_evaluations_view",
     *     type="entity",
     *     class="EvaluationEvaluationBundle:Evaluation",
     *     permission="VIEW"
     * )
     */
    public function getEvaluationsByIdMinimalAction(Request $request)
    {
        $ids = $request->get('uids');

        $evaluations = $this->getEvaluationDatabaseManagerService()->getByUids($ids);

        $data = [
            'count' => count($evaluations),
            'items' => $evaluations,
        ];

        return $this->success($data, Response::HTTP_OK, ['list']);
    }

    /**
     * @param string $evaluationUid
     *
     * @return Response
     *
     * @Acl(
     *     id="evaluation_evaluation_view",
     *     type="entity",
     *     class="EvaluationEvaluationBundle:Evaluation",
     *     permission="VIEW"
     * )
     */
    public function getEvaluationByIdAction($evaluationUid)
    {
        $evaluation = $this->getEvaluationDatabaseManagerService()->getByUid($evaluationUid);

        if (is_null($evaluation)) {
            return $this->notFound('Evaluation was not found');
        }

        return $this->success($evaluation, Response::HTTP_OK, ['list']);
    }

    /**
     * @param string $evaluationUid
     * @param string $chapterUid
     *
     * @return Response
     *
     * @Acl(
     *     id="evaluation_chapter_view",
     *     type="entity",
     *     class="EvaluationEvaluationBundle:Evaluation",
     *     permission="VIEW"
     * )
     */
    public function getChapterByIdAction($evaluationUid, $chapterUid)
    {
        $evaluation = $this->getEvaluationDatabaseManagerService()->getByUid($evaluationUid);

        if (is_null($evaluation)) {
            return $this->notFound('Evaluation was not found');
        }

        $chapter = $evaluation->getChapter($chapterUid);

        if (is_null($chapter)) {
            return $this->notFound('Chapter was not found');
        }

        return $this->success($chapter, Response::HTTP_OK, ['full']);
    }

    /**
     * @param string $evaluationUid
     * @param string $chapterUid
     *
     * @return Response
     *
     * @Acl(
     *     id="evaluation_chapter_edit",
     *     type="entity",
     *     class="EvaluationEvaluationBundle:Evaluation",
     *     permission="EDIT"
     * )
     */
    public function updateChapterAction($evaluationUid, $chapterUid)
    {
        try {
            $evaluation = $this->getEvaluationDatabaseManagerService()->getByUidForEditing($evaluationUid);

            if (is_null($evaluation)) {
                return $this->notFound('Evaluation was not found');
            }

            $chapter = $evaluation->getChapter($chapterUid);

            if (is_null($chapter)) {
                return $this->notFound('Chapter was not found');
            }

            $chapter = $this->getChapterService()->updateChapter($chapter, $this->getRequest()->getContent());

            return $this->success($chapter, Response::HTTP_OK, ['full']);
        } catch (FormException $ex) {
            return $this->clientFailure($ex->getErrors());
        }
    }

    /**
     * @param string $evaluationUid
     *
     * @return Response
     */
    public function markEvaluationAsPublishedAction($evaluationUid)
    {
        /** @var Evaluation $evaluation */
        $evaluation = $this->getEvaluationDatabaseManagerService()->getByUidForEditing($evaluationUid);

        if (is_null($evaluation)) {
            return $this->notFound('Evaluation was not found');
        }

        $evaluation->publish();

        $evaluation = $this->getEvaluationService()->updateEvaluation($evaluation);

        return $this->success($evaluation, Response::HTTP_OK, ['list']);
    }

    /**
     * @param string $evaluationUid
     *
     * @return Response
     */
    public function markEvaluationAsDraftAction($evaluationUid)
    {
        /** @var Evaluation $evaluation */
        $evaluation = $this->getEvaluationDatabaseManagerService()->getByUidForEditing($evaluationUid);

        if (is_null($evaluation)) {
            return $this->notFound('Evaluation was not found');
        }

        $evaluation->unpublish();

        $evaluation = $this->getEvaluationService()->updateEvaluation($evaluation);

        return $this->success($evaluation, Response::HTTP_OK, ['list']);
    }

    /**
     * @return ChapterService
     */
    public function getChapterService()
    {
        return $this->get('evaluation_evaluation.chapters_service');
    }

    /**
     * @return EvaluationService
     */
    public function getEvaluationService()
    {
        return $this->get('evaluation_evaluation.evaluation_service');
    }

    /**
     * @return EvaluationDataBaseManagerService
     */
    public function getEvaluationDatabaseManagerService()
    {
        return $this->get('evaluation_evaluation.evaluation_database_manager_service');
    }
}
