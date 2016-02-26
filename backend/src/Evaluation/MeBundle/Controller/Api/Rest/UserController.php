<?php

namespace Evaluation\MeBundle\Controller\Api\Rest;

use FOS\RestBundle\View\View;
use Oro\Bundle\ApiBundle\Controller\RestApiController;
use Symfony\Component\HttpFoundation\JsonResponse;

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
        $usr = $this->getUser();
        $data = array(
            'id'             => $usr->getId(),
            'username'       => $usr->getUsername(),
            'business_units' => $usr->getBusinessUnits(),
        );

        return new JsonResponse($data);
    }
}
