<?php

namespace i2c\EvaluationBundle\Controller\Api;

use i2c\EvaluationBundle\Entity\Evaluation;
use i2c\EvaluationBundle\Exception\FormException;
use i2c\EvaluationBundle\Services\Chapter as ChapterService;
use i2c\EvaluationBundle\Services\EvaluationDataBaseManager;
use i2c\EvaluationBundle\Services\Evaluation as EvaluationService;
use i2c\EvaluationBundle\Services\TableDataDatabaseManager;
use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class EvaluationController
 *
 * @package i2c\EvaluationBundle\Controller\Api
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
     *     class="i2cEvaluationBundle:Evaluation",
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
     *     class="i2cEvaluationBundle:Evaluation",
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
     *     class="i2cEvaluationBundle:Evaluation",
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
     *     class="i2cEvaluationBundle:Evaluation",
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
            return $this->clientFailure("The data you entered is invalid", $ex->getErrors());
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
     * @param string $evaluationCid
     * @param string $tableDataId
     *
     * @return Response
     */
    public function getTableDataAction($evaluationCid, $tableDataId)
    {
        /** @var Evaluation $evaluation */
        $evaluation = $this->getEvaluationDatabaseManagerService()->getByCid($evaluationCid);

        if (is_null($evaluation)) {
            return $this->notFound('Evaluation was not found');
        }

        $tableData = $this->getTableDataDatabaseManagerService()->getTableData($evaluationCid, $tableDataId);

        $response = [];

        if (!is_null($tableData)) {
            $response = $tableData->getContentAsArray();
        }

        return $this->success($response);
    }

    /**
     * @return ChapterService
     */
    public function getChapterService()
    {
        return $this->get('i2c_evaluation.chapters_service');
    }

    /**
     * @return EvaluationService
     */
    public function getEvaluationService()
    {
        return $this->get('i2c_evaluation.evaluation_service');
    }

    /**
     * @return EvaluationDataBaseManager
     */
    public function getEvaluationDatabaseManagerService()
    {
        return $this->get('i2c_evaluation.evaluation_database_manager_service');
    }

    /**
     * @return TableDataDatabaseManager
     */
    public function getTableDataDatabaseManagerService()
    {
        return $this->get('i2c_evaluation.table_data_database_manager_service');
    }
}
