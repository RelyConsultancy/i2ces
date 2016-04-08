<?php

namespace i2c\PageBundle\Controller\Api;

use i2c\EvaluationBundle\Controller\Api\RestApiController;
use i2c\EvaluationBundle\Exception\FormException;
use i2c\PageBundle\Services\Page as PageService;
use i2c\PageBundle\Services\PageDatabaseManager;
use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PageController
 *
 * @package i2c\PageBundle\Controller\Api
 */
class PageController extends RestApiController
{
    /**
     * @param string $type
     *
     * @return Response
     *
     * @Acl(
     *     id="i2c_page_view",
     *     type="entity",
     *     class="i2cPageBundle:Page",
     *     permission="VIEW"
     * )
     */
    public function getPageContentAction($type)
    {
        $page = $this->getPageDatabaseManagerService()->getPageForViewing($type);

        if (is_null($page)) {
            return $this->notFound(sprintf('Requested page `%s` was not found.', $type));
        }

        return $this->success($page, Response::HTTP_OK, ['full']);
    }

    /**
     * @param string $type
     *
     * @return Response
     *
     * @Acl(
     *     id="i2c_page_edit",
     *     type="entity",
     *     class="i2cPageBundle:Page",
     *     permission="EDIT"
     * )
     */
    public function updatePageAction($type)
    {
        try {
            $page = $this->getPageDatabaseManagerService()->getPageForEditing($type);

            if (is_null($page)) {
                return $this->notFound(sprintf('Requested page `%s` was not found.', $type));
            }

            $page = $this->getPageService()->updatePage($page, $this->getRequest()->getContent());

            return $this->success($page, Response::HTTP_OK, ['full']);
        } catch (FormException $ex) {
            return $this->clientFailure("The data you entered is invalid", $ex->getErrors());
        }
    }

    /**
     * @return PageDatabaseManager
     */
    public function getPageDatabaseManagerService()
    {
        return $this->get('i2c_page.page_database_manager_service');
    }

    /**
     * @return PageService
     */
    public function getPageService()
    {
        return $this->get('i2c_page.page_service');
    }
}
