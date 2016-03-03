<?php

namespace Evaluation\EvaluationBundle\Controller;

use Evaluation\EvaluationBundle\Services\ChapterService;
use Evaluation\EvaluationBundle\Services\EvaluationDataBaseManagerService;
use Evaluation\UtilBundle\Controller\AbstractEvaluationController;
use Evaluation\UtilBundle\Exception\FormException;
use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class EvaluationController
 *
 * @package Evaluation\EvaluationBundle\Controller
 */
class EvaluationController extends AbstractEvaluationController
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
        $ids = $request->get("ids");

        $evaluations = $this->getEvaluationDatabaseManagerService()->getByUids($ids);

        $data = [
            'count' => count($evaluations),
            'items' => $evaluations,
        ];

        return $this->getJsonResponse($data, Response::HTTP_OK, ["minimal"]);
    }

    /**
     * @param string $evaluationId
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
    public function getEvaluationByIdAction($evaluationId)
    {
        $evaluation = $this->getEvaluationDatabaseManagerService()->getByUid($evaluationId);

        if (is_null($evaluation)) {
            return $this->getNotFoundResponse("Evaluation was not found");
        }

        return $this->getJsonResponse($evaluation, Response::HTTP_OK, ["list"]);
    }

    /**
     * @param string $evaluationId
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
    public function getChapterByIdAction($evaluationId, $chapterId)
    {
        $evaluation = $this->getEvaluationDatabaseManagerService()->getByUid($evaluationId);

        if (is_null($evaluation)) {
            return $this->getNotFoundResponse("Evaluation was not found");
        }

        $chapter = $evaluation->getChapter($chapterId);

        if (is_null($chapter)) {
            return $this->getJsonResponse("Chapter was not found", Response::HTTP_NOT_FOUND);
        }

        return $this->getJsonResponse($chapter, Response::HTTP_OK, ['full']);

    }

    /**
     * @param string $evaluationId
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
    public function updateChapterAction($evaluationId, $chapterId)
    {
        $evaluation = $this->getEvaluationDatabaseManagerService()->getByUidForEditing($evaluationId);

        if (is_null($evaluation)) {
            return $this->getNotFoundResponse("Evaluation was not found");
        }

        $chapter = $evaluation->getChapter($chapterId);

        if (is_null($chapter)) {
            return $this->getNotFoundResponse("Chapter was not found");
        }

        try {
            $chapter = $this->getChapterService()->updateChapter($chapter, $this->getRequest()->getContent());

            return $this->getJsonResponse($chapter, Response::HTTP_OK, ["full"]);
        } catch (FormException $ex) {
            return $this->getJsonResponse($ex->getErrors(), Response::HTTP_CONFLICT);
        }
    }

    /**
     * @return ChapterService
     */
    public function getChapterService()
    {
        return $this->get('evaluation_evaluation.chapters_service');
    }

    /**
     * @return EvaluationDataBaseManagerService
     */
    public function getEvaluationDatabaseManagerService()
    {
        return $this->get('evaluation_evaluation.evaluation_database_manager_service');
    }
}
