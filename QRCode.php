<?php


require __DIR__.'/vendor/autoload.php';
require __DIR__.'/config-pix.php';

use \App\Pix\Payload;
use Mpdf\QrCode\QrCode;
use Mpdf\QrCode\Output;

$pixKey = $_GET['pixKey'];
if ($_GET['pixType'] == 'CEL') {
    $pixKey = '+'.$pixKey;
}
$obPayload = (new Payload)->setPixKey($pixKey)
//    ->setDescription('')
//        ->setMerchantName(PIX_MERCHANT_NAME)
//        ->setMerchantCity(PIX_MERCHANT_CITY)
    ->setAmount($_GET['amount'])
    ->setTxid('teste');
//        ->setTxid(md5(uniqid()));

    $payloadString = $obPayload->getPayload();
    $image = (new Output\Png)->output((new QrCode($payloadString)),400);

//die($payloadString);

header('Content-type: image/png');
echo $image;