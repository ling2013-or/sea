<?php
namespace Admin\Model;

use Think\Model;

/**
 * 管理员后台操作日志
 * Class AdminOperateLog
 * @package Admin\Model
 */
class AdminOperateLogModel extends Model
{
    /**
     * 自动完成
     * @var array
     */
    protected $_auto = array(
        array('add_time', NOW_TIME, self::MODEL_BOTH),
        array('add_ip', 'get_client_ip', self::MODEL_BOTH, 'function'),
    );

    /**
     * 记录操作日志
     * @param   string  $message    提示内容
     * @param   int     $status     提示状态：0-失败，1-成功
     * @return  bool
     */
    public function record($message = '', $status = 0)
    {
        $type = 'GET';
        if(IS_AJAX) {
            $type = 'AJAX';
        } elseif(IS_POST) {
            $type = 'POST';
        }
        //页面传递的参数
        $param = '';
        if($type == 'GET'){
            foreach($_GET as $key=>$val){
                $param .= $key.':'.$val.',';
            }
        }elseif($type == 'POST'){
            foreach($_POST as $key=>$val){
                $param .= $key.':'.$val.',';
            }
        }elseif($type == 'AJAX'){
            foreach($_POST as $key=>$val){
                $param .= $key.':'.$val.',';
            }
        }
        $url = isset($_SERVER['HTTP_HOST'])?'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']:'';
        $data = array(
            'uid'       => UID,
            'status'    => $status,
            'info'      => "提示语：{$message}<br/>模块：" . MODULE_NAME . ",控制器：" . CONTROLLER_NAME . ",方法：" . ACTION_NAME . "<br/>请求方式：{$type}",
            'refer'     => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '',
            'url'     => $url,
            'param'     => $param,
        );

        $this->create($data);
        return $this->add() !== false ? true : false;
    }
}