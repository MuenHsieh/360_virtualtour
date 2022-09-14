<?php
session_start();

header("Access-Control-Allow-Origin: http://localhost:3000");// cannot be a wildcard, you have to specify the name of the domain making the request here.
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true"); // add this header
/*
連線資料庫用的副程式
*/
$host = '127.0.0.1'; //執行DB Server 的主機
$user = 'root'; //登入DB用的DB 帳號
$pass = ''; //登入DB用的DB 密碼
$dbName = 'project'; //使用的資料庫名稱
/* $db 即為未來執行SQL指令所使用的物件 */
$db = mysqli_connect($host, $user, $pass, $dbName) or die('Error with MySQL connection'); //跟MySQL連線並登入

mysqli_query($db,"SET NAMES utf8"); //設定編碼為 unicode utf8
?>