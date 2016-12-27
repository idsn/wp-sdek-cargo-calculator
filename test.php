<?php
/**
 * Created by PhpStorm.
 * User: denis
 * Date: 20.12.16
 * Time: 21:12
 */

$data = array();
$data['Action'] = "GetDataByInvoice";
$data['invoice'] = "4855172";


/*$bodyData = array (
    'json' => json_encode($data)
);*/
$data_string = http_build_query(json_encode($data));

$ch = curl_init();
//curl_setopt($ch, CURLOPT_URL, "http&host=www.edostavka.ru&uri=%2fajax.php%3fJsHttpRequest%3d0-xml&t=1482257954818&sad=v%2fXVH8Pg%3d%3d&uid=x1iJFc9mVCjxx29i&uct=1482257954818&kct=0&m=4&ver=3&v=WcaERw0cjralCXH1FIKJbw");
curl_setopt($ch, CURLOPT_URL, "http://www.edostavka.ru/ajax.php?JsHttpRequest=0-xml");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//для отладки раскомментируйте и просмотрите файл errorlog.txt
//$fp = fopen(dirname(__FILE__).'/errorlog.txt', 'w');
//curl_setopt($ch, CURLOPT_VERBOSE, 1);
//curl_setopt($ch, CURLOPT_STDERR, $fp);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/x-www-form-urlencoded',
        'Content-Length: '.strlen($data_string)
    )
);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

$result = curl_exec($ch);
curl_close($ch);

echo $result;
//return json_decode($result, true);