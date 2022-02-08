<?php
require_once 'simpleMailTool.php';

$port = 465;               // 端口
$user = '46831392@qq.com'; // smtp用户名
$pass = '--------------';  // smtp密码
$host = 'smtp.qq.com';     // 服务器地址

$from   = 'kohunglee';         // 发件人昵称
$to     = '2528852314@qq.com'; // 收件人地址
$subjet = '信件标题';           // 信件标题
$body   = '<h1>hi</h1>the mail sent successful';           // 信件正文

$test = new simpleMailTool($host,$port,$user,$pass,true);  // 关闭调试只需将最后一个参数删去

// 验证用户名密码是否能连接到服务器
// echo ($test->verifyUser()) ? '<br>连接成功' : '<br>连接失败，用户名或密码错误' ;

// 发邮件测试 , 参数(发件人昵称、收件人地址、信件标题、信件正文)
// $test->sendMail($from,$to,$subjet,$body);

?>
