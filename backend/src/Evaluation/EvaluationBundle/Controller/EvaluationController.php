<?php

namespace Evaluation\EvaluationBundle\Controller;

use Evaluation\EvaluationBundle\Entity\Evaluation;
use Evaluation\EvaluationBundle\Services\Chapter as ChapterService;
use Evaluation\EvaluationBundle\Services\EvaluationDataBaseManager;
use Evaluation\EvaluationBundle\Services\Evaluation as EvaluationService;
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
    public function getEvaluationsByCidMinimalAction(Request $request)
    {
        // todo refactor into a form or something
        $cids = json_decode($request->getContent(), true)['cids'];

        $evaluations = $this->getEvaluationDatabaseManagerService()->getByCids($cids);

        $data = [
            'count' => count($evaluations),
            'items' => $evaluations,
        ];

        return $this->success($data, Response::HTTP_OK, ['list']);
    }

    /**
     * @param string $evaluationCid
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
    public function getEvaluationByCidAction($evaluationCid)
    {
        $evaluation = $this->getEvaluationDatabaseManagerService()->getByCid($evaluationCid);

        if (is_null($evaluation)) {
            return $this->notFound('Evaluation was not found');
        }

        return $this->success($evaluation, Response::HTTP_OK, ['list']);
    }

    /**
     * @param string $evaluationCid
     * @param string $chapterId
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
    public function getChapterByIdAction($evaluationCid, $chapterId)
    {
        /** @var Evaluation $evaluation */
        $evaluation = $this->getEvaluationDatabaseManagerService()->getByCid($evaluationCid);

        if (is_null($evaluation)) {
            return $this->notFound('Evaluation was not found');
        }

        $chapter = $evaluation->getChapter($chapterId);

        if (is_null($chapter)) {
            return $this->notFound('Chapter was not found');
        }

        return $this->success($chapter, Response::HTTP_OK, ['full']);
    }

    /**
     * @param string $evaluationCid
     * @param string $chapterId
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
    public function updateChapterAction($evaluationCid, $chapterId)
    {
        try {
            $evaluation = $this->getEvaluationDatabaseManagerService()->getByCidForEditing($evaluationCid);

            if (is_null($evaluation)) {
                return $this->notFound('Evaluation was not found');
            }

            $chapter = $evaluation->getChapter($chapterId);

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
     * @param string $evaluationCid
     *
     * @return Response
     */
    public function markEvaluationAsPublishedAction($evaluationCid)
    {
        /** @var Evaluation $evaluation */
        $evaluation = $this->getEvaluationDatabaseManagerService()->getByCidForEditing($evaluationCid);

        if (is_null($evaluation)) {
            return $this->notFound('Evaluation was not found');
        }

        $evaluation->publish();

        $evaluation = $this->getEvaluationService()->updateEvaluation($evaluation);

        return $this->success($evaluation, Response::HTTP_OK, ['list']);
    }

    /**
     * @param string $evaluationCid
     *
     * @return Response
     */
    public function markEvaluationAsDraftAction($evaluationCid)
    {
        /** @var Evaluation $evaluation */
        $evaluation = $this->getEvaluationDatabaseManagerService()->getByCidForEditing($evaluationCid);

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
     * @return EvaluationDataBaseManager
     */
    public function getEvaluationDatabaseManagerService()
    {
        return $this->get('evaluation_evaluation.evaluation_database_manager_service');
    }
}
