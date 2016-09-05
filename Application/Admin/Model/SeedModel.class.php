<?php
namespace Admin\Model;

use Think\Model;

/**
 * 站内消息检查
 * Class ConfigModel
 * @package Admin\Model
 */
class SeedModel extends Model
{
    /**
     * 自动验证
     * @var array
     */
    protected $_validate = array(
        array('seed_name', 'require', '种子名称不能为空！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('seed_name', '1,30', '种子名称长度1-30个字符', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
        array('seed_sn', '1,26', '种子编码长度1-26个字符', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
        array('seed_sn', '', '种子编码已存在', self::VALUE_VALIDATE, 'unique', self::MODEL_BOTH),
    );

    /**
     * 自动完成
     * @var array
     */
    protected $_auto = array(
        array('status', 1, self::MODEL_INSERT),
        array('seed_img', 'json_encode', self::MODEL_BOTH, 'function'),
        array('add_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_BOTH),
    );
}