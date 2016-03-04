<?php

namespace i2c\EvaluationBundle\Controller;

use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Oro\Bundle\ApiBundle\Controller\RestApiController as BaseController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RestApiController
 *
 * @package i2c\EvaluationBundle\Controller
 */
class RestApiController extends BaseController
{
    /**
     * Serializes the data using 'JmsSerializer' and applying the serialization groups sent
     * and builds a response with the sent status code
     *
     * @param mixed $data
     * @param int   $statusCode
     * @param array $serializationGroups
     *
     * @return Response
     */
    public function getJsonResponse($data, $statusCode = Response::HTTP_OK, $serializationGroups = [])
    {
        $view = View::create();

        $view->setData($data);
        $view->setFormat('json');

        $serializationContext = new SerializationContext();

        $serializationContext->setSerializeNull(true);

        if (count($serializationGroups) > 0) {
            $serializationContext->setGroups($serializationGroups);
        }

        $view->setSerializationContext($serializationContext);

        $view->setStatusCode($statusCode);

        return $this->handleView($view);
    }

    /**
     * @param mixed $data
     *
     * @return Response
     */
    public function getSuccessResponse($data)
    {
        return $this->getJsonResponse(
            [
                'error' => null,
                'data'  => $data,
            ],
            Response::HTTP_OK
        );
    }

    /**
     * @param string|array $message
     *
     * @return Response
     */
    public function getNotFoundResponse($message)
    {
        return $this->getJsonResponse(
            [
                'error' => $message,
                'data'  => null,
            ],
            Response::HTTP_NOT_FOUND
        );
    }

    /**
     * @param string|array $errors
     *
     * @return Response
     */
    public function getClientFailureResponse($errors)
    {
        return $this->getJsonResponse(
            [
                'error' => $errors,
                'data'  => null,
            ],
            Response::HTTP_CONFLICT
        );
    }

    /**
     * @param string|array $message
     *
     * @return Response
     */
    public function getServerFailureResponse($message)
    {
        return $this->getJsonResponse(
            [
                'error' => $message,
                'data'  => null,
            ],
            Response::HTTP_SERVICE_UNAVAILABLE
        );
    }
}
