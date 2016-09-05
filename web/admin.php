<?php
// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG', true);
define('BIND_MODULE','Admin');
// 定义应用目录
define('APP_PATH', '../Application/');
// 缓存存放目录
define('RUNTIME_PATH','../Runtime/');
// 引入ThinkPHP入口文件
require '../ThinkPHP/ThinkPHP.php';