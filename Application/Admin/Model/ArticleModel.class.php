<?php
namespace Admin\Model;

use Think\Model;

/**
 * 论坛配置
 * Class ConfigModel
 * @package Admin\Model
 */
class ArticleModel extends Model
{
    /**
     * 自动验证
     * @var array
     */
    protected $_validate = array(
        array('title', 'require', '标题不能为空', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('descript', 'require', '描述不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('status', 'require', '状态不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
    );

    /**
     * 自动完成
     * @var array
     */
    protected $_auto = array(
        array('user_id', UID, self::MODEL_INSERT),
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_BOTH),

    );

    /**
     * 获取文章列表（社区列表）
     * @return  array
     */
    public function lists($id = null,$field = 'article_id')
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