<?php
namespace Admin\Model;

use Think\Model;

/**
 * 短信模板管理
 * Class ConfigModel
 * @package Admin\Model
 */
class SmsTplModel extends Model
{
    /**
     * 自动验证
     * @var array
     */
    protected $_validate = array(
        array('name', 'require', '模板标题不能为空！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('content', 'require', '模板内容不能为空！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('alias', 'require', '模板别名不能为空！', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('alias', '', '模板别名已存在！', self::VALUE_VALIDATE, 'unique', self::MODEL_BOTH),
    );

    /**
     * 自动完成
     * @var array
     */
    protected $_auto = array(
        array('alias', 'strtoupper', self::MODEL_BOTH, 'function'),
        array('add_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_BOTH),
    );
}