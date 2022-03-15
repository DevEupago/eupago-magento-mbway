<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Eupago\MbWay\Gateway\Request;

use Magento\Framework\Exception\PaymentException;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;

class DataRequest implements BuilderInterface
{
    /**
     * Builds ENV request
     *
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        if (!isset($buildSubject['payment'])
            || !$buildSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }

        /** @var PaymentDataObjectInterface $paymentDO */
        $paymentDO = $buildSubject['payment'];
        $payment = $paymentDO->getPayment();
        $phone_number = $payment->getAdditionalInformation('phone_number');
        if (strlen($phone_number) != 9 || substr($phone_number, 0, 1) != 9) {
            throw new PaymentException(__('Número de telemóvel inválido '));
        }
        return [
            'phone_number' => $payment->getAdditionalInformation('phone_number'),
        ];
    }
}
