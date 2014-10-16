轻应用登录和平台支付Demo
===============
## Demo说明

Demo中包括百度轻应用登录和平台订单中心支付两个功能。支付中自带登录功能，但是无法获取用户信息，所以如果只需要支付功能，应用内部不维护用户和订单关系，可以略去demo中的登录，用支付自带登录即可。

## 线上体验地址
<http://lightloginpay.duapp.com/>

## 本地环境：

由于测试应用的百度登录授权回调页设置为http://localhost/ordersys/login_redirect.php。所以要在本地服务器DocumentRoot目录内新建ordersys文件夹，将代码放置此文件夹内，即应用的首页地址为http://localhost/ordersys/index.php。否则登录后会显示回调地址未授权。
