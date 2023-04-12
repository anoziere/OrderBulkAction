<?php

/*
 * This file is part of the Thelia package.
 * http://www.thelia.net
 *
 * (c) OpenStudio <info@thelia.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OrderBulkAction\Controller\Back;

use OrderBulkAction\Form\StatusForm;
use Symfony\Component\Routing\Annotation\Route;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Model\OrderQuery;
use Thelia\Model\OrderStatusQuery;

/**
 * @Route("/admin/module/OrderBulkAction", name="admin_order_bulk_action")
 */
class OrderBulkActionController extends BaseAdminController
{
    /**
     * @Route("/admin/module/OrderBulkAction/status", name="_status", methods="POST")
     */
    public function bulkStatus()
    {
        if (null !== $response = $this->checkAuth(AdminResources::ORDER, [], AccessManager::UPDATE)) {
            return $response;
        }

        $form = $this->createForm('admin_order_bulk_action_form_status');
        $params = [];
        try {
            $data = $this->validateForm($form)->getData();
            $ordersId = explode(',', $data['order_bulk_action_order_ids']);
            $statusId = $data['order_bulk_action_order_status'];
            $status = OrderStatusQuery::create()->findPk($statusId);
            if (null === $status) {
                throw new \InvalidArgumentException('The status you want to set to the order does not exist');
            }
            foreach ($ordersId as $orderId) {

                $order = OrderQuery::create()->findPk($orderId);
                if (null === $order) {
                    throw new \InvalidArgumentException('The order you want to update status does not exist');
                }
                $event = new OrderEvent($order);
                $event->setStatus($statusId);

                $this->dispatch(TheliaEvents::ORDER_UPDATE_STATUS, $event);
            }
        } catch (\Exception $e) {
            $params['update_status_error_message'] = $e->getMessage();
        }

        return $this->generateRedirectFromRoute('admin.order.list', $params);
    }
}
