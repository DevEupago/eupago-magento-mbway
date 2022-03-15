<?php
/**
 * Created by euPago.pt
 * For untitled
 * Developer: Jeferson Kaefer
 * Date: 22/11/2017
 * Time: 12:27
 */

namespace Eupago\MbWay\Controller\Callback;

use \Magento\Framework\App\Action\Action;

class MbWay extends Action
{

    public function execute()
    {

        $response = $this->allAction();
        $JsonFactory = $this->_objectManager->get('Magento\Framework\Controller\Result\JsonFactory');
        $result = $JsonFactory->create();
        $result = $result->setData($response);
        if (!isset($response['success'])) {
            $result->setHttpResponseCode(403);
        }

        return $result;
    }

    private function allAction()
    {

        $callBack_params = (object) $this->getRequest()->getParams();

        if (!$this->validaParametrosCallback($callBack_params)) {
            return ["error" => "Faltam parametros no callback"];
        }

        $orderFactory = $this->_objectManager->get('Magento\Sales\Model\OrderFactory');
        $order = $orderFactory->create()->loadByIncrementId($callBack_params->identificador);

        if ($order->getId() == null) {
            return ["error" => "a encomenda não existe"];
        }

        $metodo_callback = null;
        switch (urldecode($callBack_params->mp)) {
            case 'PC:PT':
                $metodo_callback = "eupago_multibanco";
                break;
            case 'MW:PT':
                $metodo_callback = "mb_way";
                break;
            default:
                return ["error" => "método de pagamento inválido"];
        }

        if ($order->getStatus() == "canceled") {
            return ["error" => "não foi possivel concluir o pagamento porque o estado da encomenda é: " . $order_status];
        }

        $method = $order->getPayment()->getMethod();
        if (!isset($callBack_params->mp) || $method != $metodo_callback) {
            return ["error" => "método de pagamento não corresponde ao da encomenda"];
        }

        $chave_api = $this->_objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('payment/' . $metodo_callback . '/api_key');
        if ($callBack_params->chave_api != $chave_api) {
            return ["error" => "chave API inválida", "chave" => $chave_api];
        }

        if ($order->getGrandTotal() != $callBack_params->valor) {
            return ["error" => "O valor da encomenda e o valor pago não correspondem!"];
        }

        if ($order->getBaseTotalDue() == 0) {
            return ["error" => "A encomenda já se encontra paga!"];
        }

        if ($order->getBaseTotalDue() < $callBack_params->valor) {
            return ["error" => "O valor a pagamento é inferior ao valor pago!"];
        }

        if ($this->validaTransacao($callBack_params, $order)) {
            return $this->capture($order);
        } else {
            return ["error" => "a referencia não corresponde a nenhuma transação desta encomenda."];
        }
    }

    private function validaTransacao($CallBack, $order)
    {

        $payment = $order->getPayment();
        $payment_info = $payment->getAdditionalInformation();
        if ($payment_info['referencia'] == $CallBack->referencia) {
            return true;
        }
        return false;
    }

    private function validaParametrosCallback($params)
    {
        return (isset($params->identificador, $params->valor, $params->chave_api, $params->mp, $params->referencia));
    }

    private function capture($order)
    {
        $payment = $order->getPayment();
        try {
            $payment->capture();
        } catch (\Exception $e) {
            return ["error" => $e->getMessage()];
        }
        $order->save();
        return ["success" => true, "message" => "Pagamento foi capturado com sucesso."];
    }
}
