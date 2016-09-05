<?php
namespace Admin\Model;

use Think\Model;

/**
 * 论坛配置
 * Class ConfigModel
 * @package Admin\Model
 */
class CommentModel extends Model
{
    /**
     * 自动验证
     * @var array
     */
    protected $_validate = array(
        array('content', 'require', '评论内容不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('article_id', 'require', '文章不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
    );

    /**
     * 自动完成
     * @var array
     */
    protected $_auto = array(
        array('user_id', UID, self::MODEL_INSERT),
        array('add_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_BOTH),
        array('status', 1, self::MODEL_INSERT),
    );

    /**
     * 获取文章列表（社区列表）
     * @return  array
     */
    public function lists($id = null,$field = 'comment_id')
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