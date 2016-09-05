<?php
// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG', true);
//绑定前台模块
define('BIND_MODULE', 'Home');
// 定义应用目录
define('APP_PATH', '../Application/');
// 缓存存放目录
define('RUNTIME_PATH','../Runtime/');
// 引入ThinkPHP入口文件
require '../ThinkPHP/ThinkPHP.php';