<?php

namespace App\Pix;

class Payload{

    const ID_PAYLOAD_FORMAT_INDICATOR = '00';
    const ID_POINT_OF_INITIATION_METHOD = '01';
    const ID_MERCHANT_ACCOUNT_INFORMATION = '26';
    const ID_MERCHANT_ACCOUNT_INFORMATION_GUI = '00';
    const ID_MERCHANT_ACCOUNT_INFORMATION_KEY = '01';
    const ID_MERCHANT_ACCOUNT_INFORMATION_DESCRIPTION = '02';
    const ID_MERCHANT_ACCOUNT_INFORMATION_URL = '25';
    const ID_MERCHANT_CATEGORY_CODE = '52';
    const ID_TRANSACTION_CURRENCY = '53';
    const ID_TRANSACTION_AMOUNT = '54';
    const ID_COUNTRY_CODE = '58';
    const ID_MERCHANT_NAME = '59';
    const ID_MERCHANT_CITY = '60';
    const ID_ADDITIONAL_DATA_FIELD_TEMPLATE = '62';
    const ID_ADDITIONAL_DATA_FIELD_TEMPLATE_TXID = '05';
    const ID_CRC16 = '63';

    private $pixKey;
    private $description;
    private $merchantName;
    private $merchantCity;
    private $txid;
    private $amount;
    private $uniquePayment = false;
    private $url;

    public function setPixKey($pixKey)
    {
        $this->pixKey = $pixKey;
        return $this;
    }

    public function setUniquePayment($uniquePayment)
    {
        $this->uniquePayment = $uniquePayment;
        return $this;
    }

    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    public function setMerchantName($merchantName)
    {
        $this->merchantName = $merchantName;
        return $this;
    }

    public function setMerchantCity($merchantCity)
    {
        $this->merchantCity = $merchantCity;
        return $this;
    }

    public function setTxid($txid)
    {
        $this->txid = $txid;
        return $this;
    }

    public function setAmount($amount)
    {
        $this->amount = (string)number_format($amount,2,'.','');
        return $this;
    }

    private function getValue($id,$value)
    {
        $size = str_pad(mb_strlen($value),2,'0',STR_PAD_LEFT);
        return $id.$size.$value;
    }

    private function getMerchantAccountInformation()
    {
        //DOMÍNIO DO BANCO
        $gui = $this->getValue(self::ID_MERCHANT_ACCOUNT_INFORMATION_GUI,'br.gov.bcb.pix');

        //CHAVE PIX
        $key = strlen($this->pixKey) ? $this->getValue(self::ID_MERCHANT_ACCOUNT_INFORMATION_KEY,$this->pixKey) : '';

        //DESCRIÇÃO DO PAGAMENTO
        $description = strlen($this->description) ? $this->getValue(self::ID_MERCHANT_ACCOUNT_INFORMATION_DESCRIPTION,$this->description) : '';

        //URL DO QR CODE DINÂMICO
        $url = strlen($this->url) ? $this->getValue(self::ID_MERCHANT_ACCOUNT_INFORMATION_URL,preg_replace('/^https?\:\/\//','',$this->url)) : '';

        //VALOR COMPLETO DA CONTA
        return $this->getValue(self::ID_MERCHANT_ACCOUNT_INFORMATION,$gui.$key.$description.$url);
    }

    private function getAdditionalDataFieldTemplate()
    {
        //TXID
        $txid = $this->getValue(self::ID_ADDITIONAL_DATA_FIELD_TEMPLATE_TXID,$this->txid);

        //RETORNA O VALOR COMPLETO
        return $this->getValue(self::ID_ADDITIONAL_DATA_FIELD_TEMPLATE,$txid);
    }

    private function getUniquePayment()
    {
        return $this->uniquePayment ? $this->getValue(self::ID_POINT_OF_INITIATION_METHOD,'12') : '';
    }

    public function getPayload()
    {
        return $this->getCRC16WithPayload(implode('', [
            $this->getValue(self::ID_PAYLOAD_FORMAT_INDICATOR,'01'),
            $this->getUniquePayment(),
            $this->getMerchantAccountInformation(),
            $this->getValue(self::ID_MERCHANT_CATEGORY_CODE,'0000'),
            $this->getValue(self::ID_TRANSACTION_CURRENCY,'986'),
            $this->getValue(self::ID_TRANSACTION_AMOUNT,$this->amount),
            $this->getValue(self::ID_COUNTRY_CODE,'BR'),
            $this->getValue(self::ID_MERCHANT_NAME,$this->merchantName),
            $this->getValue(self::ID_MERCHANT_CITY,$this->merchantCity),
            $this->getAdditionalDataFieldTemplate()
        ]));
    }

    private function getCRC16WithPayload($payload)
    {
        $str = $payload.self::ID_CRC16.'04';
        $crc = 0xFFFF;
        $strlen = strlen($str);
        for ($c = 0; $c < $strlen; $c++) {
            $crc ^= ord(substr($str, $c, 1)) << 8;
            for ($i = 0; $i < 8; $i++) {
                if ($crc & 0x8000) {
                    $crc = ($crc << 1) ^ 0x1021;
                } else {
                    $crc = $crc << 1;
                }
            }
        }
        $hex = $crc & 0xFFFF;
        $hex = dechex($hex);
        $hex = strtoupper($hex);

        return $str.$hex;
    }

}