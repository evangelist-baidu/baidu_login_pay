<?php
require_once('./loginsdk/BaiduApiClient.php');
require_once('./loginsdk/BaiduOAuth2.php');
require_once('./loginsdk/BaiduUtils.php');
require_once('./inc/lightapp_login_api.inc.php');

//回调页地址
$redirectUri = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];

$code = $_GET['code'];

//echo $code;
//echo $redirectUri;

$oauth = new BaiduOAuth2($lightapp_api_key, $ligthapp_secret_key);
$oauth->setRedirectUri($redirectUri);

$tokenArr = $oauth->getAccessTokenByAuthorizationCode($code);
if (is_array($tokenArr)) {
    // 换取token成功
    $accessToken = $tokenArr['access_token'];
    $expires_in = $tokenArr['expires_in'];

    // 获取用户信息
    $client = new BaiduApiClient($lightapp_api_key, $accessToken);
    $infoArr = $client->api('/rest/2.0/passport/users/getInfo', array('fields' => 'userid,username,portrait'));
    if (is_array($infoArr)) {
        // 获取用户信息成功
        // 在这里将百度账号与应用自身的账号系统做联合登录处理，建议采取将百度账号暗绑到自身账号体系上
        // 然后将联合登录后生成的用户session的相关信息通过cookie返回到前端页面上
        // 为方便处理，这里将access_token和百度用户uid直接当session信息塞入cookie
        setcookie('bd_access_token', $accessToken, strtotime('2030-1-1 12:00:00'), '/');
        setcookie('bd_username', $infoArr['username'], strtotime('2030-1-1 12:00:00'), '/');
        setcookie('bd_uid', $infoArr['userid'], strtotime('2030-1-1 12:00:00'), '/');
        setcookie('bd_portrait', $infoArr['portrait'], strtotime('2030-1-1 12:00:00'), '/');

    }
}

?>

<!DOCTYPE html>
<html>
<head></head>
<body>
<script>
    window.parent.onSuccess();
</script>
</body>
</html>
