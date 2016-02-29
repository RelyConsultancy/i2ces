<?php

namespace Evaluation\EvaluationBundle\Controller;

use Evaluation\EvaluationBundle\Entity\Evaluation;
use Evaluation\EvaluationBundle\Repository\EvaluationRepository;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Oro\Bundle\ApiBundle\Controller\RestApiController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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
     * @return JsonResponse
     */
    public function getByIdsAction(Request $request)
    {
        $ids = $request->get("ids");

        $evaluations = $this->getEvaluationsRepository()->getByUids($ids);

        $view = View::create($evaluations);
        $view->setFormat('json');
        $serializationContext = new SerializationContext();
        $serializationContext->enableMaxDepthChecks();
        $serializationContext->setSerializeNull(true);
        $view->setSerializationContext($serializationContext);

        return $this->handleView($view);
    }

    /**
     * @return EvaluationRepository
     */
    protected function getEvaluationsRepository()
    {
        return $this->getDoctrine()->getRepository("EvaluationEvaluationBundle:Evaluation");
    }
}
