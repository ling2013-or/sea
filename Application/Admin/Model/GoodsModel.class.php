<?php
namespace Admin\Model;

use Think\Model;

/**
 * 商品管理模型
 * Class OrderModel
 * @package Admin\Model
 */
class GoodsModel extends Model
{
    /**
     * 自动验证
     */
    protected $_validate = array(
        array('name', 'require', '请填写商品名称', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
    );

    /**
     * 自动完成
     */
    protected $_auto = array(
        array('picture_more', 'json_encode', self::MODEL_BOTH, 'function'),
        array('add_time', 'time', self::MODEL_INSERT, 'function'),
        array('price', 'floatval', self::MODEL_BOTH, 'function'),
        array('mark_price', 'floatval', self::MODEL_BOTH, 'function'),
//        array('seed_id', 'get_seed_id', self::MODEL_BOTH, 'callback'),
//        array('farm_id', 'get_farm_id', self::MODEL_BOTH, 'callback'),
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