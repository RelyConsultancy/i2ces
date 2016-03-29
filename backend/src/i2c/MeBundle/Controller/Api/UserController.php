<?php

namespace i2c\MeBundle\Controller\Api;

use i2c\EvaluationBundle\Services\EvaluationDataBaseManager;
use i2c\MeBundle\Services\BusinessUnit;
use FOS\RestBundle\View\View;
use i2c\EvaluationBundle\Controller\Api\RestApiController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserController
 *
 * @package i2c\MeBundle\Controller\Api
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
        $isEmployee = $this->get('oro_security.security_facade')
            ->isGranted('EDIT', 'Entity:i2cEvaluationBundle:Evaluation');

        $user = $this->getUser();

        if ($isEmployee) {
            $viewEvaluations = $this->getEvaluationDatabaseManagerService()->getAllForViewing();
        } else {
            $viewEvaluations = $this->getEvaluationDatabaseManagerService()->getAllPublishedForViewing();
        }

        $editEvaluations = $this->getEvaluationDatabaseManagerService()->getAllForEditing();

        $data = array(
            'id'             => $user->getId(),
            'username'       => $user->getUsername(),
            'business_units' => $this->getBusinessUnitService()->getBusinessUnitsForUserAsArray($user),
            'view'           => $viewEvaluations,
            'edit'           => $editEvaluations,
        );

        if ($isEmployee) {
            $data['type'] = 'i2c_employee';
        } else {
            $data['type'] = 'supplier';
        }
        return $this->success($data, Response::HTTP_OK, 'minimal');
    }

    /**
     * @return EvaluationDataBaseManager
     */
    public function getEvaluationDatabaseManagerService()
    {
        return $this->get('i2c_evaluation.evaluation_database_manager_service');
    }

    /**
     * @return BusinessUnit
     */
    public function getBusinessUnitService()
    {
        return $this->get('i2c_me.business_unit_service');
    }
}
