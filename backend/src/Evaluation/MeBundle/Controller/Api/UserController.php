<?php

namespace Evaluation\MeBundle\Controller\Api;

use Evaluation\EvaluationBundle\Services\EvaluationDataBaseManager;
use Evaluation\MeBundle\Services\BusinessUnit;
use FOS\RestBundle\View\View;
use i2c\EvaluationBundle\Controller\Api\RestApiController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserController
 *
 * @package Evaluation\MeBundle\Controller\Api
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
     * @return EvaluationDataBaseManager
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
