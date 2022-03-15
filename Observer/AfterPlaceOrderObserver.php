<?php

namespace Eupago\MbWay\Observer;

use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;

class AfterPlaceOrderObserver extends AbstractDataAssignObserver
{
    /**
     * Order Model
     *
     * @var \Magento\Sales\Model\Order $order
     */
    protected $order;

    public function __construct(
        \Magento\Sales\Model\Order $order
    ) {
        $this->order = $order;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/payment_method_debug.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $orderId = $observer->getEvent()->getOrderIds();
        $order = $this->order->load($orderId);
        $currentState = $order->getState();

        $save = false;
        if ($currentState !== $order::STATE_NEW) {
            $order->setState($order::STATE_PENDING_PAYMENT);
            $order->setStatus('pending');
            $save = true;
        }
        if ($save) {
            $order->save();
        }

        $logger->info('[CHECKOUT] Order: ' . json_encode($orderId) . ' Current state: ' . json_encode($currentState) . ' Order status:' . json_encode($order->getStatus()));
    }
}
