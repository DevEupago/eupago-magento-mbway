<?php
/**
 * Created by euPago.pt
 * For untitled
 * Developer: Jeferson Kaefer
 * Date: 23/11/2017
 * Time: 10:39
 */

namespace Eupago\MbWay\Block;

class Info extends \Magento\Payment\Block\Info
{

    public function getSpecificInformation()
    {
        $informations['Telemovel'] = $this->getInfo()->getAdditionalInformation('phone_number');
        $informations['Referencia'] = $this->getInfo()->getAdditionalInformation('referencia');
        return (object) $informations;
    }

    public function getMethodCode()
    {
        return $this->getInfo()->getMethodInstance()->getCode();
    }

}
