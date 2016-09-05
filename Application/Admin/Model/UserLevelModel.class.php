<?php
namespace Admin\Model;

use Think\Model;

/**
 * 会员等级管理
 * Class ConfigModel
 * @package Admin\Model
 */
class UserLevelModel extends Model
{
    /**
     * 自动验证
     * @var array
     */
    protected $_validate = array(
        array('level_name', '', '等级名称已存在', self::VALUE_VALIDATE, 'unique', self::MODEL_BOTH),
        array('level_name', 'require', '名称不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('cash_highter', 'require', '最高消费金额不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('cash_lower', 'require', '最低消费金额不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('cash_highter', '_checkCash', '请检查上限/下限数值范围', self::MUST_VALIDATE, 'callback', self::MODEL_BOTH),
        array('cash_lower', '_checkCash', '请检查上限/下限数值范围', self::MUST_VALIDATE, 'callback', self::MODEL_BOTH),
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

    /**
     * 获取文章列表（社区列表）
     * @return  array
     */
    public function lists($id = null, $field = 'level_id')
    {
        $where = array();
        if ($id || I('id')) {
            if ($id) {
                $id = $id;
            } else {
                $id = I('id');
            }
            $where[$field] = $id;
        }
        $data = $this->field('*')->where($where)->select();
        return $data;
    }

    //验证上限/下限是否与别的值存在交集
    public function _checkCash(){
        $where = array();
        $cash_highter = I('post.cash_highter');
        $cash_lower = I('post.cash_lower');

        $map['cash_highter'] = array('between',$cash_lower .','. $cash_highter);
        $map['cash_lower'] = array('between',$cash_lower .','. $cash_highter);
        $level_id = I('post.level_id');
        if($level_id){
            $where['level_id'] = array('neq',$level_id);
        }
        $map['_logic'] = 'or';
        $where['_complex'] = $map;
        $level = M('UserLevel');
        $result = $level->where($where)->count();
//        var_dump($result);die;
        if($result){
            return false;
        }
        return true;
    }


}