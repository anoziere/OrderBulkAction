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

namespace OrderBulkAction\Hook;

use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;

class OrdersTableHook extends BaseHook
{
    public function onOrdersTableHeader(HookRenderEvent $event): void
    {
        $content = $this->render('hook/header-col-orders.html');
        $event->add($content);
    }

    public function onOrdersTableRow(HookRenderEvent $event): void
    {
        if($event->getArgument('location') === 'orders_table_row') {
            $content = $this->render('hook/row-orders.html', [
                'order_id' => $event->getArgument('order_id'),
            ]);
            $event->add($content);
        }
    }

    public function onOrdersTop(HookRenderEvent $event): void
    {
        $content = $this->render('hook/top-select-bulk-actions.html');
        $event->add($content);

    }

    public function onOrdersJs(HookRenderEvent $event): void
    {
        $content = $this->render('hook/js-bulk-actions.html');

        $event->add($content);
    }

    public static function getSubscribedHooks(): array
    {
        return [
            'orders.table-header' => [
                [
                    'type' => 'back',
                    'method' => 'onOrdersTableHeader',
                ],
            ],
            'orders.table-row' => [
                [
                    'type' => 'back',
                    'method' => 'onOrdersTableRow',
                ],
            ],
            'orders.js' => [
                [
                    'type' => 'back',
                    'method' => 'onOrdersJs',
                ],
            ],
            'orders.top' => [
                [
                    'type' => 'back',
                    'method' => 'onOrdersTop',
                ],
            ],
        ];
    }
}
