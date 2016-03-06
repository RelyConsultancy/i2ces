<?php

namespace Evaluation\UtilBundle\Controller;

use Doctrine\ORM\EntityManager;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Oro\Bundle\ApiBundle\Controller\RestApiController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AbstractEvaluationController
 *
 * @package Evaluation\UtilBundle\Controller
 */
class AbstractEvaluationController extends RestApiController
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
     * @param string $message
     *
     * @return Response
     */
    public function getNotFoundResponse($message)
    {
        return $this->getJsonResponse(
            [
                "error" => $message,
            ],
            Response::HTTP_NOT_FOUND
        );
    }

    /**
     * Deserialized the entity fromt the request using a json format
     *
     * @param string $class full class name including namespace
     *
     * @return array|mixed|object
     *
     * @throws \Exception
     */
    public function getDeserializedEntityFromRequest($class)
    {
        return $this->get('serializer')->deserialize(
            $this->getRequest()->getContent(),
            $class,
            'json'
        );
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->get('doctrine')->getEntityManager();
    }
}
