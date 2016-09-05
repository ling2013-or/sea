<?php
/**
 * Created by PhpStorm.
 * User: suntianqi
 * Date: 2016/7/28
 * Time: 16:05
 */

namespace Admin\Model;


use Think\Model;

class CarouselModel extends Model {
    /**
     * 自动验证
     */
    protected $_validate = array(
        array('title', 'require', '请填写标题', self::MUST_VALIDATE, 'unique', self::MODEL_BOTH),
        array('model', 'require', '请填写导航模块标识', self::MUST_VALIDATE, 'unique', self::MODEL_BOTH),
    );

    /**
     * 自动完成
     */
    protected $_auto = array(
        array('img', 'json_encode', self::MODEL_BOTH, 'function'),
        array('url', 'json_encode', self::MODEL_BOTH, 'function'),
        array('add_time', 'time', self::MODEL_INSERT, 'function'),
        array('num', 'intval', self::MODEL_BOTH, 'function'),
    );

} 