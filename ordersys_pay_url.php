<?php
/**
 * Created by PhpStorm.
 * User: yt
 * Date: 14-10-8
 * Time: 下午12:25
 */

if (!defined("SERVER_ROOT")) {
    define("SERVER_ROOT", str_replace('ordersys_pay_url.php','',"http://".$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME']));
}

if (!defined("FILE_ROOT")) {
    define("FILE_ROOT", dirname(__FILE__) . DIRECTORY_SEPARATOR);
}

require_once(FILE_ROOT . 'inc/pay_api.inc.php');

$order_no = "test-".strtotime(date('Y-m-d H:i:s'));
$return_url = SERVER_ROOT ."pay_return_url.php";
$page_url = SERVER_ROOT ."pay_success.php";
$order_source_url = SERVER_ROOT ."index.php";

if(isset($_POST['params'])){
    $post_params = json_decode($_POST['params'],true);
    $params = array(
        'sp_no' => ordersys_conf::SP_NO,
        'order_no' => $order_no,
        'total_amount' => (string)$post_params['total_amount'],
        'goods_name' => $post_params['goods_name'],
        'return_url' => $return_url,
        'page_url' => $page_url,
        'detail' => json_encode($post_params['detail']),
        'order_source_url' => $order_source_url,
        'customer_name' => $post_params['customer_name'],
        'customer_mobile' => $post_params['customer_mobile'],
        'customer_address' => $post_params['customer_address']
    );
    $data = makePostParamsUrl($params);

    $ch = curl_init ();
    $url = ordersys_conf::ORDER_ADD_PAY_URL;

    curl_setopt ( $ch, CURLOPT_URL, $url );
    curl_setopt ( $ch, CURLOPT_POST, 1 );
    curl_setopt ( $ch, CURLOPT_HEADER, 0 );
    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
    $return = curl_exec ( $ch );
    curl_close ( $ch );

    echo $return;
}else{
    $error_arr = array(
        "error_code" => 001,
        "error_msg"=> "no params, please check!"
    );
    echo json_encode($error_arr);
}

function getSignature($params, $session_secret)
{
    if (is_array($params) && is_string($session_secret)) {
        if (ksort($params)) {
            $string_temp = '';
            foreach ($params as $key => $val) {
                $string_temp .= $key . '=' . $val;
            }
            $string_temp .= $session_secret;
            return md5($string_temp);

        } else {
            return false;
        }
    } else {
        return false;
    }
}


function makePostParamsUrl($params)
{
    if (is_array($params)) {
        $sign = getSignature($params, ordersys_conf::SESSION_SECRET);
        if (is_string($sign)) {
            $arr_temp = array();
            foreach ($params as $key => $val) {
                $arr_temp[$key]= $val;
            }
            $arr_temp['sign'] = $sign;
            $str_url = http_build_query($arr_temp);
            return $str_url;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

?>