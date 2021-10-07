<?php

namespace Pipirima\PimcoreKeeperBundle\Controller\Admin;

use Pipirima\PimcoreKeeperBundle\Service\ObjectKeeperService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/object")
 */
class DataObjectController extends \Pimcore\Bundle\AdminBundle\Controller\Admin\DataObject\DataObjectController
{
    protected ObjectKeeperService $objectKeeperService;

    /**
     * @Route("/save", name="pimcore_admin_dataobject_dataobject_save", methods={"POST", "PUT"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws \Exception
     */
    public function saveAction(Request $request)
    {
        $id = intval($request->get('id'));
        $data = strval($request->get('data'));
        if ($id && $data) {
            $this->objectKeeperService->processSaveObjectData($id, $this->decodeJson($data));
        }
        return parent::saveAction($request);
    }

    /**
     * @param ObjectKeeperService $objectKeeperService
     */
    public function setObjectKeeperService(ObjectKeeperService $objectKeeperService)
    {
        $this->objectKeeperService = $objectKeeperService;
    }
}
