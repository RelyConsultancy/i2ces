<?php

namespace Evaluation\MeBundle\Controller\Api\Rest;

use Evaluation\EvaluationBundle\Services\EvaluationDataBaseManagerService;
use Evaluation\MeBundle\Services\BusinessUnit;
use FOS\RestBundle\View\View;
use i2c\EvaluationBundle\Controller\RestApiController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserController
 *
 * @package Evaluation\MeBundle\Controller
 */
class UserController extends RestApiController
{
    /**
     * Returns a json with the current user data.
     *
     * @return View
     */
    public function getMeAction()
    {
        $user = $this->getUser();

        $viewEvaluations = $this->getEvaluationDatabaseManagerService()->getAllForViewing();

        $editEvaluations = $this->getEvaluationDatabaseManagerService()->getAllForEditing();

        $data = array(
            'id'             => $user->getId(),
            'username'       => $user->getUsername(),
            'business_units' => $this->getBusinessUnitService()->getBusinessUnitsForUserAsArray($user),
            'view'           => $viewEvaluations,
            'edit'           => $editEvaluations,
        );

        return $this->success($data, Response::HTTP_OK, 'minimal');
    }

    /**
     * @return EvaluationDataBaseManagerService
     */
    public function getEvaluationDatabaseManagerService()
    {
        return $this->get('evaluation_evaluation.evaluation_database_manager_service');
    }

    /**
     * @return BusinessUnit
     */
    public function getBusinessUnitService()
    {
        return $this->get('evaluation_me.business_unit_service');
    }
}
