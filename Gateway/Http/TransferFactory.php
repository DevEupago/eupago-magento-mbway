<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Eupago\MbWay\Gateway\Http;

use Magento\Payment\Gateway\Http\TransferBuilder;
use Magento\Payment\Gateway\Http\TransferFactoryInterface;
use Magento\Payment\Gateway\Http\TransferInterface;

class TransferFactory implements TransferFactoryInterface
{

    /**
     * @var TransferBuilder
     */
    public static $SERVER = [
        'dev' => 'https://sandbox.eupago.pt/replica.eupagov20.wsdl',
        'production' => 'https://clientes.eupago.pt/eupagov20.wsdl',
    ];

    private $transferBuilder;
    private $api_key;

    /**
     * @param TransferBuilder $transferBuilder
     */
    public function __construct(
        TransferBuilder $transferBuilder
    ) {
        $this->transferBuilder = $transferBuilder;
    }

    /**
     * Builds gateway transfer object
     *
     * @param array $request
     * @return TransferInterface
     */
    public function create(array $request)
    {
        $this->api_key = $request['data_request']['chave'];
        return $this->transferBuilder
            ->setBody($request['data_request'])
            ->setMethod($request['method'])
            ->setClientConfig(
                [
                    'wsdl' => $this->getServerURI(),
                ]
            )
            ->build();
    }

    private function getServerURI()
    {
        $api_key = stripos($this->api_key, 'demo');
        if ($api_key !== false) {
            return self::$SERVER['dev'];
        }
        return self::$SERVER['production'];
    }

}
