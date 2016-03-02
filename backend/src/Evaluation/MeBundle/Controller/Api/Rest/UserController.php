<?php

namespace Evaluation\MeBundle\Controller\Api\Rest;

use Evaluation\UtilBundle\Controller\AbstractEvaluationController;
use Evaluation\UtilBundle\Helpers\BusinessUnitHelper;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserController
 *
 * @package Evaluation\MeBundle\Controller
 */
class UserController extends AbstractEvaluationController
{
    /**
     * Returns a json with the current user data.
     *
     * @return View
     */
    public function getMeAction()
    {
        $user = $this->getUser();

        $data = array(
            'id'             => $user->getId(),
            'username'       => $user->getUsername(),
            'business_units' => BusinessUnitHelper::getBusinessUnitCollectionAsArray($user->getBusinessUnits()),
        );

        return $this->getJsonResponse($data, Response::HTTP_OK);
    }
}
