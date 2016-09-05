<?php
namespace Admin\Model;

use Think\Model;

/**
 * 站内消息检查
 * Class ConfigModel
 * @package Admin\Model
 */
class SensitiveVocabularyModel extends Model
{
    /**
     * 自动验证
     * @var array
     */
    protected $_validate = array(
        array('vocabulary', 'require', '标题不能为空', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
    );

    /**
     * 自动完成
     * @var array
     */
    protected $_auto = array(
        array('uid', UID, self::MODEL_INSERT),
        array('add_time', NOW_TIME, self::MODEL_INSERT),
        array('status', 1, self::MODEL_INSERT),
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