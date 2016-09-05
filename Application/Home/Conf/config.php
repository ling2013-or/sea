<?php
return array(
	//'配置项'=>'配置值'
    /* 数据缓存设置 */
    'DATA_CACHE_PREFIX'    => '',   // 缓存前缀(暂时不添加，保持各个模块前缀统一：如:短信接口配置)
    'DATA_CACHE_TYPE'      => 'File', // 数据缓存类型
    'URL_MODEL'            => 3, //URL模式

    /* 文件上传相关配置 */
    'DOWNLOAD_UPLOAD' => array(
        'mimes'    => '', //允许上传的文件MiMe类型
        'maxSize'  => 5*1024*1024, //上传的文件大小限制 (0-不做限制)
        'exts'     => 'jpg,gif,png,jpeg,zip,rar,tar,gz,7z,doc,docx,txt,xml', //允许上传的文件后缀
        'autoSub'  => true, //自动子目录保存文件
        'subName'  => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath' => './uploads/Download/', //保存根路径
        'savePath' => '', //保存路径
        'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt'  => '', //文件保存后缀，空则使用原后缀
        'replace'  => false, //存在同名是否覆盖
        'hash'     => true, //是否生成hash编码
        'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
    ), //下载模型上传配置（文件上传类配置）

    /* 图片上传相关配置 */
    'PICTURE_UPLOAD' => array(
        'mimes'    => '', //允许上传的文件MiMe类型
        'maxSize'  => 2*1024*1024, //上传的文件大小限制 (0-不做限制)
        'exts'     => 'jpg,gif,png,jpeg', //允许上传的文件后缀
        'autoSub'  => true, //自动子目录保存文件
        'subName'  => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath' => './uploads/Picture/', //保存根路径
        'savePath' => '', //保存路径
        'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt'  => '', //文件保存后缀，空则使用原后缀
        'replace'  => false, //存在同名是否覆盖
        'hash'     => true, //是否生成hash编码
        'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
    ), //图片上传相关配置（文件上传类配置）

    'PICTURE_UPLOAD_DRIVER'=>'local',
    //本地上传文件驱动配置
    'UPLOAD_LOCAL_CONFIG'=>array(),

    /* 模板相关配置 */
    'TMPL_PARSE_STRING' => array(
        '__STATIC__' => __ROOT__ . '/statics/static',
        '__ASSET__' => __ROOT__ . '/statics/assets',
        '__IMG__'    => __ROOT__ . '/statics/' . MODULE_NAME . '/img',
        '__CSS__'    => __ROOT__ . '/statics/' . MODULE_NAME . '/css',
        '__JS__'     => __ROOT__ . '/statics/' . MODULE_NAME . '/js',
    ),

    /* SESSION 和 COOKIE 配置 */
    'SESSION_PREFIX' => 'farm_admin', //session前缀
    'COOKIE_PREFIX'  => 'farm_admin_', // Cookie前缀 避免冲突
    'VAR_SESSION_ID' => 'session_id',	//修复uploadify插件无法传递session_id的bug
);