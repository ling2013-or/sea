<?php
namespace Admin\Model;

use Think\Model;

/**
 * 站内消息检查
 * Class ConfigModel
 * @package Admin\Model
 */
class MessageModel extends Model
{
    /**
     * 自动验证
     * @var array
     */
    protected $_validate = array(
        array('title', 'require', '标题不能为空', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('type', 'require', '类型不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('content', 'require', '内容不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('uid', 'require', '收信人不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
    );

    /**
     * 自动完成
     * @var array
     */
    protected $_auto = array(
//        array('admin_id', UID, self::MODEL_INSERT),
        array('add_time', NOW_TIME, self::MODEL_INSERT),
        array('status', 2, self::MODEL_INSERT),
    );

    /**
     * 获取文章列表（社区列表）
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