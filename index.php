<?php

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/config-pix.php';

use \App\Pix\Payload;
use Mpdf\QrCode\QrCode;
use Mpdf\QrCode\Output;

$obPayload = (new Payload)->setPixKey(PIX_KEY)
//    ->setDescription('')
    ->setMerchantName(PIX_MERCHANT_NAME)
    ->setMerchantCity(PIX_MERCHANT_CITY)
    ->setAmount(0.01)
    ->setTxid(md5(uniqid()));

$payloadString = $obPayload->getPayload();
$image = (new Output\Png)->output((new QrCode($payloadString)),400);

?>

<h1>QR CODE PIX</h1>

<br>

<img src="data:image/png;base64, <?=base64_encode($image)?>">

<br><br>

Código copia e cola:<br>
<strong><? echo $payloadString?></strong>