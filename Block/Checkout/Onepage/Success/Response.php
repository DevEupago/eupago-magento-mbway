<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Eupago\MbWay\Block\Checkout\Onepage\Success;

class Response extends \Magento\Checkout\Block\Onepage\Success
{
    /**
     * Prepares block data
     *
     * @return void
     */
    protected $order;

    protected function _construct()
    {
        $this->setModuleName('Magento_Checkout');
        parent::_construct();
    }

    protected function prepareBlockData()
    {

        $this->order = $this->_checkoutSession->getLastRealOrder();
        $this->addData(
            [
                'phone_number' => $this->order->getPayment()->getAdditionalInformation('phone_number'),
                'grand_total' => $this->getFormatValue(),
                'order_id' => $this->order->getIncrementId(),
//                'is_order_visible' => $this->isVisible($order),
                //                'view_order_url' => $this->getUrl(
                //                    'sales/order/view/',
                //                    ['order_id' => $order->getEntityId()]
                //                ),
                //                'print_url' => $this->getUrl(
                //                    'sales/order/print',
                //                    ['order_id' => $order->getEntityId()]
                //                ),
                //                'can_print_order' => $this->isVisible($order),
                //                'can_view_order' => $this->canViewOrder($order),
                //                'delivery_method' => $order->getAddressShippingMethod(),
                //                'items' => $order->getAllItems()
            ]
        );

    }

    private function getFormatValue()
    {
        return number_format($this->order->getGrandTotal(), '2', '.', ',');
    }

    public function isMethodEupago()
    {
        if ($this->order->getPayment()->getMethod() == \Eupago\MbWay\Model\Ui\ConfigProvider::CODE) {
            return true;
        }
        return false;
    }
}
