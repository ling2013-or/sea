<?php
/**
 * 系统配文件
 * 所有系统级别的配置
 */
return array(
    /* 模块相关配置 */
    'DEFAULT_MODULE' => 'Home',
    'MODULE_DENY_LIST' => array('Common', 'Admin', 'Api'),

    /* 用户相关设置 */
    'USER_ADMINISTRATOR' => 1, //管理员用户ID

    /* URL配置 */
    'URL_CASE_INSENSITIVE' => true, //默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_MODEL' => 3, //URL模式
    'VAR_URL_PARAMS' => '', // PATHINFO URL参数变量
    'URL_PATHINFO_DEPR' => '/', //PATHINFO URL分割符

    /* 全局过滤配置 */
    'DEFAULT_FILTER' => '', //全局过滤函数

    /* 数据库配置 */
    'DB_TYPE' => 'mysql', // 数据库类型
//    'DB_HOST' => 'localhost', // 服务器地址
    'DB_HOST' => '115.28.4.172', // 服务器地址
    'DB_NAME' => 'sea', // 数据库名
//    'DB_USER' => 'root', // 用户名
//    'DB_PWD' => '123456',  // 密码
    'DB_USER' => 'liujianjian', // 用户名
    'DB_PWD' => 'ljj123456',  // 密码
    'DB_PORT' => '3306', // 端口
    'DB_PREFIX' => 'sea_', // 数据库表前缀

);
