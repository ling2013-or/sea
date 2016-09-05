<?php
namespace Admin\Model;

use Think\Model;

/**
 * 摄像头管理模型
 * Class CameraModel
 * @package Admin\Model
 */
class CameraModel extends Model
{
    /**
     * 自动验证
     * TODO IP地址正则校验
     * @var array
     */
    protected $_validate = array(
        array('farm_id', 'require', '所属农场不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('title', 'require', '名称不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('camera_id', 'require', '摄像头ID不能为空', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('camera_id', '', '标识已经存在', self::VALUE_VALIDATE, 'unique', self::MODEL_BOTH),
        array('server_ip', 'require', 'IP地址不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('server_port', 'number', '端口号格式不正确', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
    );

    /**
     * 自动完成
     * @var array
     */
    protected $_auto = array(
        array('add_time', NOW_TIME, self::MODEL_INSERT),
        array('uid', UID, self::MODEL_BOTH),
    );
}