<?php
/**
 * Примеры запросов:
 * По объёму: /wp-content/plugins/wp-plg-sdek-calculator/calc.php?cityTo=337&weight=15&volume=0.002&type=v
 * По метрикам: /wp-content/plugins/wp-plg-sdek-calculator/calc.php?cityTo=337&weight=10&l=10&w=15&h=10&type=s
 */
header('Content-Type: application/json');
/** @noinspection PhpIncludeInspection */
$parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
require_once( $parse_uri[0] . 'wp-load.php' );
require_once ("./CalculatePriceDeliveryCdek.php");

$calc = new CalculatePriceDeliveryCdek();
$calc->setAuth(
    get_option('sdek_authLogin'),
    get_option('sdek_authPassword'));


$daysShift = get_option('sdek_dateExecuteShift');

$sendingDate=Date('Y-m-d', strtotime("+$daysShift days"));
$calc->setDateExecute($sendingDate);
$calc->setSenderCityId(get_option('sdek_senderCityId'));

$calc->setModeDeliveryId(get_option('sdek_modeId'));
$calc->setTariffId(get_option('sdek_tariffDefault'));
foreach(explode("\n", get_option('sdek_tariffList')) as $l){
    $explode = explode("=", trim($l));
    $calc->addTariffPriority(trim($explode[1]), trim($explode[0]));
}

$calc->setReceiverCityId($_REQUEST['cityTo']);
if ($_REQUEST['type']=="v") {
    $calc->addGoodsItemByVolume($_REQUEST['weight'], $_REQUEST['volume']);
}else if($_REQUEST['type']=="s"){
    $calc->addGoodsItemBySize($_REQUEST['weight'], $_REQUEST['l'], $_REQUEST['w'], $_REQUEST['h']);
}

if ($calc->calculate() === true) {
    $res = $calc->getResult();
    $response = array(
        'price' => $res['result']['price'],
        'time_min' => $res['result']['deliveryPeriodMin'],
        'time_max' => $res['result']['deliveryPeriodMax'],
        'date_min' => $res['result']['deliveryDateMin'],
        'date_max' => $res['result']['deliveryDateMax']
    );
} else {
    $err = $calc->getError();
    if( isset($err['error']) && !empty($err) ) {
        //var_dump($err);
        foreach($err['error'] as $e) {
            $response = array(
                'last_error' => $e['code'],
                'last_error_text' => $e['text']
            );
        }
    }
}

echo json_encode($response);
?>