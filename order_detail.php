<?php
/**
 * Created by PhpStorm.
 * User: yt
 * Date: 14-10-8
 * Time: 上午10:21
 */


$total_amount = 2;
$goods_name = "订单名称";
$detail = array(
    array(
    'item_id' => 'po8348865999721745',
    'cat_id' => 0,
    'name' => '日本寿司',
    'desc' => '很好吃',
    'price' => 1,
    'amount' => 1,
    ),
    array(
        'item_id' => 'po9293477665438182',
        'cat_id' => 0,
        'name' => '肯德基外卖全家桶',
        'desc' => '实惠',
        'price' => 1,
        'amount' => 1,
    ),
);

$customer_name = "客户名称";
$customer_mobile = "1860000000";
$customer_address = "北京市北京市海淀区上地东路20号";

$params = array(
    'total_amount' => (string)$total_amount,
    'goods_name' => $goods_name,
    'detail' => $detail,
    'customer_name' => $customer_name,
    'customer_mobile' => $customer_mobile,
    'customer_address' => $customer_address
);

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
    订单确认
</div>
<div class="main">

    <p>订单金额(分):<?php echo $total_amount ?></p>

    <p>订单名称:<?php echo $goods_name ?></p>

    <p>买家的用户名:<?php echo $customer_name ?></p>

    <p>买家的手机号:<?php echo $customer_mobile ?></p>

    <p>买家的地址:<?php echo $customer_address ?></p>

    <p>订单信息:<br>
        <?php
        foreach ($detail as $item) {
            echo implode('-', $item);
            echo '<br>';
        }
        ?>
    </p>

</div>
<div class="footer">
    提交订单
</div>
</body>
<script>
    $('.footer').click(function (event) {
        postToOrderSys('<?php echo json_encode($params) ?>');
    });

    function postToOrderSys(orderInfo) {
        if (orderInfo) {
            console.log(orderInfo);
            $.ajax({
                url: 'ordersys_pay_url.php',
                type: 'post',
                timeout: 10000,
                data: {params: orderInfo},
                dataType: "json",
                contentType: "application/x-www-form-urlencoded",
                success: function (data, textStatus) {
                    if (data['error_code']) {
                        console.log(data['error_msg']);
                    } else {
                        location.href = data['url'];
                        console.log(data['order_id']);
                    }
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    console.log(errorThrown);
                }
            });
        }
    }
</script>
</html>