<?php
/**
 * Created by PhpStorm.
 * User: yt
 * Date: 14-10-8
 * Time: 下午3:59
 * */

//省略 验证订单支付，修改订单状态，

if(isset($_GET['order_no'])){
    $order_no = $_GET['order_no'];
};
if(isset($_GET['order_id'])){
    $order_id = $_GET['order_id'];
};
if(isset($_GET['sp_no'])){
    $sp_no = $_GET['sp_no'];
};
if(isset($_GET['pay_time'])){
    $pay_time = date('Y-m-d H:i:s',$_GET['pay_time']);
};
if(isset($_GET['pay_result'])){
    $pay_result = $_GET['pay_result'];
};
if(isset($_GET['paid_amount'])){
    $paid_amount = $_GET['paid_amount'];
};
if(isset($_GET['coupons'])){
    $coupons = $_GET['coupons'];
};
if(isset($_GET['promotion'])){
    $promotion = $_GET['promotion'];
};


?>

<!DOCTYPE html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
    <meta name="format-detection" content="telephone=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <title>订单确认</title>
    <script src="js/jquery-2.1.1.min.js"></script>
    <script src="js/main.js"></script>
    <link rel="stylesheet" href="css/main.css"/>
</head>
<body>
<div class="header">
    支付结果
</div>
<div class="main">
    <?php if(isset($order_no)) { ?>
         <p>第三方的订单号:<?php echo $order_no?></p>
    <?php } ?>
    <?php if(isset($order_id)) { ?>
        <p>轻应用中心订单号:<?php echo $order_id?></p>
    <?php } ?>
    <?php if(isset($pay_time)) { ?>
        <p>支付时间:<?php echo $pay_time?></p>
    <?php } ?>
    <?php if(isset($paid_amount)) { ?>
        <p>支付金额(分):<?php echo $paid_amount?></p>
    <?php } ?>
    <?php if(isset($pay_result)) { ?>
        <p>支付结果:<?php echo $pay_result?>(1支付成功 2等待支付 3退款成功)</p>
    <?php } ?>


</div>