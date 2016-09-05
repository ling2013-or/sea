<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
namespace Think;
/**
 * ThinkPHP 应用程序类 执行应用过程管理
 */
class App {

    /**
     * 应用程序初始化
     * @access public
     * @return void
     */
    static public function init() {
        // 加载动态应用公共文件和配置
        load_ext_file(COMMON_PATH);

        // 日志目录转换为绝对路径 默认情况下存储到公共模块下面
        C('LOG_PATH',   realpath(LOG_PATH).'/Common/');

        // 定义当前请求的系统常量
        define('NOW_TIME',      $_SERVER['REQUEST_TIME']);
        define('REQUEST_METHOD',$_SERVER['REQUEST_METHOD']);
        define('IS_GET',        REQUEST_METHOD =='GET' ? true : false);
        define('IS_POST',       REQUEST_METHOD =='POST' ? true : false);
        define('IS_PUT',        REQUEST_METHOD =='PUT' ? true : false);
        define('IS_DELETE',     REQUEST_METHOD =='DELETE' ? true : false);

        // URL调度
        Dispatcher::dispatch();

        if(C('REQUEST_VARS_FILTER')){
			// 全局安全过滤
			array_walk_recursive($_GET,		'think_filter');
			array_walk_recursive($_POST,	'think_filter');
			array_walk_recursive($_REQUEST,	'think_filter');
		}

        // URL调度结束标签
        Hook::listen('url_dispatch');         

        define('IS_AJAX',       ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || !empty($_POST[C('VAR_AJAX_SUBMIT')]) || !empty($_GET[C('VAR_AJAX_SUBMIT')])) ? true : false);

        // TMPL_EXCEPTION_FILE 改为绝对地址
        C('TMPL_EXCEPTION_FILE',realpath(C('TMPL_EXCEPTION_FILE')));
        return ;
    }

    /**
     * 执行应用程序
     * @access public
     * @return void
     */
    static public function exec() {
    
        if(!preg_match('/^[A-Za-z](\/|\w)*$/',CONTROLLER_NAME)){ // 安全检测
            $module  =  false;
        }elseif(C('ACTION_BIND_CLASS')){
            // 操作绑定到类：模块\Controller\控制器\操作
            $layer  =   C('DEFAULT_C_LAYER');
            if(is_dir(MODULE_PATH.$layer.'/'.CONTROLLER_NAME)){
                $namespace  =   MODULE_NAME.'\\'.$layer.'\\'.CONTROLLER_NAME.'\\';
            }else{
                // 空控制器
                $namespace  =   MODULE_NAME.'\\'.$layer.'\\_empty\\';                    
            }
            $actionName     =   strtolower(ACTION_NAME);
            if(class_exists($namespace.$actionName)){
                $class   =  $namespace.$actionName;
            }elseif(class_exists($namespace.'_empty')){
                // 空操作
                $class   =  $namespace.'_empty';
            }else{
                E(L('_ERROR_ACTION_').':'.ACTION_NAME);
            }
            $module  =  new $class;
            // 操作绑定到类后 固定执行run入口
            $action  =  'run';
        }else{
            //创建控制器实例
            $module  =  controller(CONTROLLER_NAME,CONTROLLER_PATH);                
        }

        if(!$module) {
            // 是否定义Empty控制器
            $module = A('Empty');
            if(!$module){
                E(L('_CONTROLLER_NOT_EXIST_').':'.CONTROLLER_NAME);
            }
        }

        // 获取当前操作名 支持动态路由
        if(!isset($action)){
            $action    =   ACTION_NAME.C('ACTION_SUFFIX');  
        }
        try{
            self::invokeAction($module,$action);
        } catch (\ReflectionException $e) { 
            // 方法调用发生异常后 引导到__call方法处理
            $method = new \ReflectionMethod($module,'__call');
            $method->invokeArgs($module,array($action,''));
        }
        return ;
    }
    public static function invokeAction($module,$action){
	if(!preg_match('/^[A-Za-z](\w)*$/',$action)){
		// 非法操作
		throw new \ReflectionException();
	}
	//执行当前操作
	$method =   new \ReflectionMethod($module, $action);
	if($method->isPublic() && !$method->isStatic()) {
		$class  =   new \ReflectionClass($module);
		// 前置操作
		if($class->hasMethod('_before_'.$action)) {
			$before =   $class->getMethod('_before_'.$action);
			if($before->isPublic()) {
				$before->invoke($module);
			}
		}
		// URL参数绑定检测
		if($method->getNumberOfParameters()>0 && C('URL_PARAMS_BIND')){
			switch($_SERVER['REQUEST_METHOD']) {
				case 'POST':
					$vars    =  array_merge($_GET,$_POST);
					break;
				case 'PUT':
					parse_str(file_get_contents('php://input'), $vars);
					break;
				default:
					$vars  =  $_GET;
			}
			$params =  $method->getParameters();
			$paramsBindType     =   C('URL_PARAMS_BIND_TYPE');
			foreach ($params as $param){
				$name = $param->getName();
				if( 1 == $paramsBindType && !empty($vars) ){
					$args[] =   array_shift($vars);
				}elseif( 0 == $paramsBindType && isset($vars[$name])){
					$args[] =   $vars[$name];
				}elseif($param->isDefaultValueAvailable()){
					$args[] =   $param->getDefaultValue();
				}else{
					E(L('_PARAM_ERROR_').':'.$name);
				}   
			}
			// 开启绑定参数过滤机制
			if(C('URL_PARAMS_SAFE')){
				$filters     =   C('URL_PARAMS_FILTER')?:C('DEFAULT_FILTER');
				if($filters) {
					$filters    =   explode(',',$filters);
					foreach($filters as $filter){
						$args   =   array_map_recursive($filter,$args); // 参数过滤
					}
				}                        
			}
			array_walk_recursive($args,'think_filter');
			$method->invokeArgs($module,$args);
		}else{
			$method->invoke($module);
		}
		// 后置操作
		if($class->hasMethod('_after_'.$action)) {
			$after =   $class->getMethod('_after_'.$action);
			if($after->isPublic()) {
				$after->invoke($module);
			}
		}
	}else{
		// 操作方法不是Public 抛出异常
		throw new \ReflectionException();
	}
    }
    /**
     * 运行应用实例 入口文件使用的快捷方法
     * @access public
     * @return void
     */
    static public function run() {
        // 应用初始化标签
        Hook::listen('app_init');
        App::init();
        // 应用开始标签
        Hook::listen('app_begin');
        // Session初始化
        if(!IS_CLI){
            session(C('SESSION_OPTIONS'));
        }
        // 记录应用初始化时间
        G('initTime');
        App::exec();
        // 应用结束标签
        Hook::listen('app_end');
        return ;
    }

    static public function logo(){
        return 'iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6RDc3RTFBRTFFQzdDMTFFNEFGRTBBQjRFOTg2RjFGRDgiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6RDc3RTFBRTJFQzdDMTFFNEFGRTBBQjRFOTg2RjFGRDgiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpENzdFMUFERkVDN0MxMUU0QUZFMEFCNEU5ODZGMUZEOCIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpENzdFMUFFMEVDN0MxMUU0QUZFMEFCNEU5ODZGMUZEOCIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/Psv9qrEAAAZQSURBVHja7JoJbBVV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';
    }
}
