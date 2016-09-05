<?php
namespace Admin\Model;

use Think\Model;

/**
 * 农场发货地址
 * Class ConfigModel
 * @package Admin\Model
 */
class DaddressModel extends Model
{
    /**
     * 自动验证
     * @var array
     */
    protected $_validate = array(
        array('are_id', 'require', '所属地不能为空', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('farm_id', 'require', '农场不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('area_info', 'require', '详细地址不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('seller_name', 'require', '姓名不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('telphone', 'require', '姓名不能为空', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('telphone', '/^1[0-9]{10}$/', '请输入正确的电话号码', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
    );

    /**
     * 自动完成
     * @var array
     */
    protected $_auto = array(
        array('uid', UID, self::MODEL_INSERT),
        array('add_time', NOW_TIME, self::MODEL_INSERT),
        array('status', 0, self::MODEL_INSERT),
    );

    /**
     * 获取发货地址（发货地址列表）
     * @return  array
     */
    public function lists($id = null,$field = 'id')
    {
        $where = array();
        if($id || I('id')){
            if($id){
                $id = $id;
            }else{
                $id = I('id');
            }
            $where[$field] = $id;
        }
        $data = $this->field('*')->where($where)->select();
        return $data;
    }


}