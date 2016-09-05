<?php
namespace Admin\Model;

use Think\Model;

/**
 * 物流公司
 * Class ConfigModel
 * @package Admin\Model
 */
class ExpressModel extends Model
{
    /**
     * 自动验证
     * @var array
     */
    protected $_validate = array(
        array('name', 'require', '公司名称不能为空', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('code', 'require', '验证码不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('listorder', 'require', '详细地址不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('url', 'require', '姓名不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
    );

    /**
     * 自动完成
     * @var array
     */
    protected $_auto = array();

    /**
     * 获取发货地址（发货地址列表）
     * @return  array
     */
    public function lists($id = null, $field = 'id')
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
        $where['status'] = array('neq', '-1');
        $data = $this->field('*')->where($where)->select();
        return $data;
    }


}