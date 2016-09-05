<?php
/**
 * Created by PhpStorm.
 * User: TEST01
 * Date: 2016/7/21
 * Time: 11:56
 */

namespace Admin\Model;

use Think\Model;
class PlanSellModel extends Model {
    /**
     * 自动验证
     */
    protected $_validate = array(
        array('title', 'require', '请填写计划标题', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
    );

    /**
     * 自动完成
     */
    protected $_auto = array(
        array('pic', 'json_encode', self::MODEL_BOTH, 'function'),
        array('add_time', 'time', self::MODEL_INSERT, 'function'),
        array('month', 'intval', self::MODEL_BOTH, 'function'),
    );

    /**
     * 获取种子id
     */
    public function get_seed_id()
    {
        $plan_id = I('post.plan_id', 0, 'intval');
        $seed = M('PlanSell')->field('seed_id')->find($plan_id);
        if ($seed) {
            return  $seed['seed_id'];
        } else {
            return 0;
        }
    }

    /**
     * 获取农场id
     */
    public function get_farm_id()
    {
        $plan_id = I('post.plan_id', 0, 'intval');
        $seed = M('PlanSell')->field('farm_id')->find($plan_id);
        if ($seed) {
            return  $seed['farm_id'];
        } else {
            return 0;
        }
    }
} 