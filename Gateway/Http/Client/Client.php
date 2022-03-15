<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Eupago\MbWay\Gateway\Http\Client;

use Magento\Framework\Webapi\Soap\ClientFactory;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\ConverterInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Payment\Model\Method\Logger;

/**
 * Class Soap
 * @package Magento\Payment\Gateway\Http\Client
 * @api
 */
class Client implements ClientInterface
{
    const SUCCESS = [
        'paga',
        'transferida',
        'em processamento',
    ];
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var ConverterInterface | null
     */
    private $converter;
    /**
     * @var ClientFactory
     */
    private $clientFactory;

    /**
     * @param Logger $logger
     * @param ClientFactory $clientFactory
     * @param ConverterInterface | null $converter
     */
    public function __construct(Logger $logger, ClientFactory $clientFactory, ConverterInterface $converter = null)
    {
        $this->logger = $logger;
        $this->converter = $converter;
        $this->clientFactory = $clientFactory;
    }

    /**
     * Places request to gateway. Returns result as ENV array
     *
     * @param TransferInterface $transferObject
     * @return array
     * @throws \Magento\Payment\Gateway\Http\ClientException
     * @throws \Magento\Payment\Gateway\Http\ConverterException
     * @throws \Exception
     */
    public function placeRequest(TransferInterface $transferObject)
    {

        $client = $this->clientFactory->create($transferObject->getClientConfig()['wsdl'], ['trace' => true]);
        try {
            $client->__setSoapHeaders($transferObject->getHeaders());
            $response = $client->__soapCall($transferObject->getMethod(), [$transferObject->getBody()]);
            $result = $this->converter ? $this->converter->convert($response) : [$response];
        } catch (\Exception $e) {
            throw $e;
        }
        return $result;
    }

}
