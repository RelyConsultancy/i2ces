<?php

namespace i2c\EvaluationBundle\Controller\Api;

use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Oro\Bundle\ApiBundle\Controller\RestApiController as BaseController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RestApiController
 *
 * @package i2c\EvaluationBundle\Controller\Api
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
     * @param int   $statusCode
     * @param array $serializationGroups
     *
     * @return Response
     */
    public function success($data, $statusCode = Response::HTTP_OK, $serializationGroups = [])
    {
        return $this->getJsonResponse(
            $data,
            $statusCode,
            $serializationGroups
        );
    }

    /**
     * @param string|array $message
     * @param int          $statusCode
     *
     * @return Response
     */
    public function notFound($message, $statusCode = Response::HTTP_NOT_FOUND)
    {
        return $this->clientFailure($message, [], $statusCode);
    }

    /**
     * @param string $message
     * @param array  $data
     * @param int    $statusCode
     *
     * @return Response
     */
    public function clientFailure($message, $data, $statusCode = Response::HTTP_CONFLICT)
    {
        if (!is_array($data)) {
            $data = array($data);
        }

        return $this->getJsonResponse(
            [
                'message' => $message,
                'data'    => $data,
            ],
            $statusCode
        );
    }

    /**
     * @param string|array $message
     * @param int          $statusCode
     *
     * @return Response
     */
    public function serverFailure($message, $statusCode = Response::HTTP_SERVICE_UNAVAILABLE)
    {
        return $this->getJsonResponse(
            [
                'message' => $message,
                'data'    => [],
            ],
            $statusCode
        );
    }

    /**
     * Checks the role of the logged in user and returns false if the user has a read only role on evaluations or true
     * otherwise
     *
     * @return bool
     */
    protected function isLoggedInUserEmployee()
    {
        return $this->get('oro_security.security_facade')
                    ->isGranted('EDIT', 'Entity:i2cEvaluationBundle:Evaluation');
    }
}
