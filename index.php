<?php
    require_once('./inc/lightapp_login_api.inc.php');
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

<!--    注意 应用首页代码,从该轻应用-》状态信息-》编辑中获取，用于提交轻应用时验证用。-->
    <script type="text/javascript" name="baidu-tc-cerfication" data-appid="5022168" src="http://apps.bdimg.com/cloudaapi/lightapp.js"></script>

    <script type="text/javascript">
        clouda.lightInit({
            ak: '<?=$lightapp_api_key?>',
            module: ["account","smartBar"]
        },function(){
            clouda.lego.smartBar.adjustPanel({"selector":"#bottomBar"});
        });
    </script>

</head>
<body>
<div class="header">
    Demo首页
</div>
<div class="main">

    <div>
        <button id="loginBtn"></button>
    </div>

    <div id="userInfo">
        <p>用户信息</p>
        <img id='user_portrait'src="" alt=""/>
        <p>access_token: <span id='access_token' style="color: #FF533C"></span> </p>
        <p>uid:<span id='uid' style="color: #FF533C"></span></p>
        <p>username:<span id='username' style="color: #FF533C"></span></p>
    </div>

</div>
<div class="footer" id="bottomBar">
    查看购物车
</div>
</body>
<script>
    $('.footer').click(function (event) {
        location.href = "http://"+location.hostname+location.pathname+"order_detail.php";
    });

    $('#loginBtn').click(function (event) {
        isLogin()?logout():login();
    });

    //登陆成功后的处理函数
    function onSuccess(){
        clouda.mbaas.account.closeLoginDialog();
        displayUserInfo();
    }

    //登陆页面点击“回退”后的处理函数
    function onFail() {
        alert('没有登录成功！');
    }

    function login(){
        //使用轻应用登陆接口登陆
        clouda.mbaas.account.login({
            redirect_uri: location.href.replace(/(.*\/).*/, "$1")+"login_redirect.php",
            scope:'basic',
            // mobile: mobile,
            display: 'mobile',
            login_mode: 1,
            login_type:'sms',
            state:'hello_state',
            onsuccess: onSuccess,
            onfail: onFail

        });
    }

    function displayUserInfo(){
        if(!isLogin()) {
            $('#userInfo').hide();
            $('#loginBtn').text('登录');
        } else {
            $('#user_portrait')[0].src = 'http://tb.himg.baidu.com/sys/portrait/item/'+getCookie('bd_portrait')
            $('#access_token').text(getCookie('bd_access_token'));
            $('#uid').text(getCookie('bd_uid'));
            $('#username').text(getCookie('bd_username'));

            $('#userInfo').show();
            $('#loginBtn').text('退出');
        }
    }

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

    function isLogin(){
        return getCookie('bd_uid') !== null;
    }

    function logout(){
        delCookie('bd_uid');
        displayUserInfo();
    }

    function getCookie(name)
    {
        var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");

        if(arr=document.cookie.match(reg))

            return unescape(arr[2]);
        else
            return null;
    }

    function delCookie(name)
    {
        var exp = new Date();
        exp.setTime(exp.getTime() - 1000);
        var cval=getCookie(name);
        if(cval!=null)
            document.cookie= name + "="+cval+";expires="+exp.toGMTString()+";path=/";
    }

    function setCookie (name, value)
    {
        document.cookie = name + "=" + escape(value) +";path=/" ;
    }



    displayUserInfo();

    function checkCookie(){
        if(getCookie('testCookie') == null) {
            setCookie('testCookie',1);
        }

        if(getCookie('testCookie') == null) {
            alert('禁用了cookie。禁用cookie会导致百度登录失败。');
        } else {
            alert('继续试用demo！');
        }

        delCookie('testCookie');
    }

    checkCookie();

</script>
</html>

