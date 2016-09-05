<?php
/**
 * Created by PhpStorm.
 * User: TEST01
 * Date: 2016/7/21
 * Time: 18:09
 */

namespace Admin\Model;


use Think\Model;

class UserModel extends Model {
    /**
     * 自动验证
     * @var array
     */
    protected $_validate = array(
        array('user_phone', '', '手机号码已存在', self::VALUE_VALIDATE, 'unique', self::MODEL_BOTH),
        array('user_name', 'require', '用户名已存在', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),

    );

    /**
     * 自动完成
     * @var array
     */
    protected $_auto = array(
        array('uid', UID, self::MODEL_INSERT),
        array('add_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_BOTH),
        array('operator', UID, self::MODEL_BOTH),
    );
} 