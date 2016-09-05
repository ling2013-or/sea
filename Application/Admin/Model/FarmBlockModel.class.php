<?php

namespace Admin\Model;

use Think\Model;

/**
 * 农场分区管理模型
 * Class FarmModel
 * @package Admin\Model
 */
class FarmBlockModel extends Model
{
    /**
     * 自动验证
     * @var array
     */
    protected $_validate = array(
        array('block_sn', '1,20', '农场分区编码长度4-20个字符', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
        array('block_sn', '', '农场分区编码已存在', self::VALUE_VALIDATE, 'unique', self::MODEL_BOTH),
        array('block_name', '/^[a-zA-Z0-9_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]+$/', '农场名不能存在特殊字符', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('block_name', '', '农场分区名已存在', self::VALUE_VALIDATE, 'unique', self::MODEL_BOTH),
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