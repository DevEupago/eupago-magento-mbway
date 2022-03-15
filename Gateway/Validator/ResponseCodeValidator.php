<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Eupago\MbWay\Gateway\Validator;

use Eupago\MbWay\Gateway\Http\Client\Client;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterface;

class ResponseCodeValidator extends AbstractValidator
{
    private $value_referencia;
    private $value_order;

    /**
     * Performs validation of result code
     *
     * @param array $validationSubject
     * @return ResultInterface
     */
    public function validate(array $validationSubject)
    {
        if (!isset($validationSubject['response']) || !is_array($validationSubject['response'])) {
            throw new \InvalidArgumentException('Response does not exist');
        }

        $response = $validationSubject['response'];
        $paymentDO = SubjectReader::readPayment($validationSubject);

        $this->value_order = number_format($paymentDO->getOrder()->getGrandTotalAmount(), '2', '.', ',');
        $this->value_referencia = number_format($response[0]->valor, '2', '.', ',');

        if ($this->isSuccessfulTransaction($response)) {
            $payment = $paymentDO->getPayment();
            $payment->setIsTransactionClosed(true);
            return $this->createResult(
                true,
                [__('Deu bom.')]
            );
        } else {
            return $this->createResult(
                false,
                [__('Deu ruin.')]
            );
        }
    }

    /**
     * @param array $response
     * @return bool
     */
    private function isSuccessfulTransaction(array $response)
    {
        if (in_array($response[0]->estado_referencia, Client::SUCCESS)
            && ($this->value_order == $this->value_referencia)) {
            return true;
        }
        return false;
    }
}
