<?php

namespace Admin\Model;

use Think\Model;

/**
 * 农场管理模型
 * Class FarmModel
 * @package Admin\Model
 */
class FarmModel extends Model
{
    /**
     * 自动验证
     * @var array
     */
    protected $_validate = array(
        array('farm_sn', '1,20', '农场编码长度4-20个字符', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
        array('farm_sn', '', '农场编码已存在', self::VALUE_VALIDATE, 'unique', self::MODEL_BOTH),
        array('farm_name', '/^[a-zA-Z0-9_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]+$/', '农场名不能存在特殊字符', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('farm_name', '', '农场名已存在', self::VALUE_VALIDATE, 'unique', self::MODEL_BOTH),
        array('farm_name', '1,16', '农场名称长度为1-16个字符', self::EXISTS_VALIDATE, 'length', self::MODEL_BOTH),

        array('owner_email', 'email', '邮箱格式不正确', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH),
        array('owner_mobile', '/^1[0-9]{10}$/', '手机号码格式不正确', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH),
    );

    /**
     * 自动完成
     * @var array
     */
    protected $_auto = array(
        array('status', 1, self::MODEL_INSERT, 'string'),
        array('add_time',NOW_TIME, self::MODEL_INSERT, 'string'),
        array('update_time',NOW_TIME, self::MODEL_BOTH, 'string'),
    );
}