<?php

namespace Evaluation\EvaluationBundle\Controller;

use Evaluation\EvaluationBundle\Entity\Chapter;
use Evaluation\EvaluationBundle\Entity\Evaluation;
use Evaluation\EvaluationBundle\Repository\EvaluationRepository;
use Evaluation\UtilBundle\Controller\AbstractEvaluationController;
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
     *     id="evaluation_evaluation_view",
     *     type="entity",
     *     class="EvaluationEvaluationBundle:Evaluation",
     *     permission="VIEW"
     * )
     */
    public function getEvaluationsByIdMinimalAction(Request $request)
    {
        $ids = $request->get("ids");

        $aclHelper = $this->get('oro_security.acl_helper');

        $evaluations = $this->getEvaluationsRepository()->getByUids($ids, $aclHelper, ['VIEW']);

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
        $aclHelper = $this->get('oro_security.acl_helper');

        $evaluation = $this->getEvaluationsRepository()->getByUid($evaluationId, $aclHelper, ['VIEW']);

        if (is_null($evaluation)) {
            return new Response("Evaluation not found", Response::HTTP_NOT_FOUND);
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
     *     id="evaluation_evaluation_view",
     *     type="entity",
     *     class="EvaluationEvaluationBundle:Evaluation",
     *     permission="VIEW"
     * )
     */
    public function getChapterByIdAction($evaluationId, $chapterId)
    {
        $aclHelper = $this->get('oro_security.acl_helper');

        $evaluation = $this->getEvaluationsRepository()->getByUid($evaluationId, $aclHelper, ['VIEW']);

        if (is_null($evaluation)) {
            return new Response("Evaluation not found", Response::HTTP_NOT_FOUND);
        }

        foreach ($evaluation->getChapters() as $chapter) {
            if ($chapterId == $chapter->getId()) {
                return $this->getJsonResponse($chapter, Response::HTTP_OK, ["full"]);
            }
        }

        return $this->getJsonResponse("Chapter was not found", Response::HTTP_NOT_FOUND);
    }

    /**
     * @param string $evaluationId
     * @param string $chapterId
     *
     * @return Response
     *
     * @Acl(
     *     id="evaluation_evaluation_view",
     *     type="entity",
     *     class="EvaluationEvaluationBundle:Evaluation",
     *     permission="EDIT"
     * )
     */
    public function updateChapterAction($evaluationId, $chapterId)
    {
        $aclHelper = $this->get('oro_security.acl_helper');

        $evaluation = $this->getEvaluationsRepository()->getByUid($evaluationId, $aclHelper, ['VIEW']);

        if (is_null($evaluation)) {
            return new Response("Evaluation not found", Response::HTTP_NOT_FOUND);
        }

        $foundChapter = null;

        /** @var Chapter $chapter */
        foreach ($evaluation->getChapters() as $chapter) {
            if ($chapterId == $chapter->getId()) {
                $foundChapter = $chapter;
                break;
            }
        }

        if (is_null($foundChapter)) {
            return $this->getJsonResponse("Chapter was not found", Response::HTTP_NOT_FOUND);
        }

        /** @var Chapter $sentChapter */
        $sentChapter = $this->getDeserializedEntityFromRequest('Evaluation\EvaluationBundle\Entity\Chapter');

        $foundChapter->setContent(json_encode($sentChapter->getContent()));

        $foundChapter->setLastModifiedAt(new \DateTime('now'));
        $foundChapter->setTitle($sentChapter->getTitle());
        $foundChapter->setState($sentChapter->getState());

        $this->getEntityManager()->persist($foundChapter);

        $this->getEntityManager()->flush();

        $this->getEntityManager()->refresh($foundChapter);

        return $this->getJsonResponse($chapter, Response::HTTP_OK, ["full"]);
    }

    /**
     * @return EvaluationRepository
     */
    protected function getEvaluationsRepository()
    {
        return $this->getDoctrine()->getRepository("EvaluationEvaluationBundle:Evaluation");
    }
}
