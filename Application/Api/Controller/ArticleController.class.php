<?php
namespace Api\Controller;

/**
 * 文章 消息 评论管理
 * Class ArticleController
 * @package Api\Controller
 */
class ArticleController extends ApiController
{
    /**
     * 文章列表（社区列表）
     * @param string title 查询条件标题
     * @param string article_id 查询条件 文章/社区ID
     * @return array data 列表数据
     * @return array list 文章具体数据
     * @return bool status 查询状态（接口状态）
     * @return int code 查询结果码（0状态码）
     * @return int user_id 文章/社区创建者ID
     * @return int status 文章状态 0禁用  1开启
     * @return int article_id 文章ID
     * @return int praise 文章被赞数
     * @return int views 文章浏览量
     * @return int add_time 文章创建时间
     * @return int update_time 文章修改时间
     * @return string msg 接口结果描述
     * @return string true_name 创建者真实姓名
     * @return string title 文章标题
     * @return string descript 文章描述
     * @return string content 文章内容
     * @return string carousel 文章图片
     */
    public function article()
    {
        /*搜索条件开始*/
        $where = array();
        $where['status'] = array('eq', 1);
        //标题
        if (isset($this->data['title']) && !empty($this->data['title'])) {
            $map['title'] = array('like', '%' . trim($this->data['title'] . '%'));
        }

        //搜索条件并列
        if (isset($map['title']) && isset($map['article_id'])) {
            $map['_logic'] = 'or';
            $where['_complex'] = $map;
        } else {
            $where = $map;
        }
        /*搜索条件结束*/

        /*分页开始*/
        //计算分页
        if (isset($this->data['page']) && intval($this->data['page']) > 0) {
            $this->page = intval($this->data['page']);
        }
        if (isset($this->data['page_size']) && intval($this->data['page_size']) > 0) {
            $this->page_size = intval($this->data['page_size']);
        }
        $limit = ($this->page - 1) * $this->page_size . ',' . $this->page_size;
        // 获取总条数
        $total = M('Article')->where($where)->count();
        /*分页结束*/
        $lists = M('Article')
            ->field('*')
            ->where($where)
            ->order('article_id DESC')
            ->limit($limit)
            ->select();
        $data = array(
            'page' => $this->page,
            'count' => $total,
            'list' => $lists ? $lists : '',
        );
        $this->apiReturn(0, '成功', $data);
    }

    /**
     * 文章详情
     * @param string title 查询条件标题
     * @param string article_id 查询条件 文章/社区ID
     * @return array list 文章具体数据
     * @return bool status 查询状态（接口状态）
     * @return int code 查询结果码（0状态码）
     * @return int user_id 文章/社区创建者ID
     * @return int status 文章状态 0禁用  1开启
     * @return int article_id 文章ID
     * @return int praise 文章被赞数
     * @return int views 文章浏览量
     * @return int add_time 文章创建时间
     * @return int update_time 文章修改时间
     * @return string true_name 创建者真实姓名
     * @return string title 文章标题
     * @return string descript 文章描述
     * @return string content 文章内容
     * @return string carousel 文章图片
     */
    public function details()
    {
        $where = array();
        $where['status'] = array('eq', 1);
        //获取文章ID
        if (!isset($this->data['article_id']) || empty($this->data['article_id'])) {
            $this->apiReturn(60001, '文章ID不能为空');
        }
        $model = M('Article');
        $where['article_id'] = array('eq', $this->data['article_id']);
        $lists = $model
            ->field('*')
            ->where($where)
            ->find();
        if (empty($lists)) {
            $this->apiReturn(60002, '文章不存在');
        }
        $data = array(
            'list' => $lists,
        );
        //增加文章的浏览量
        $view = $this->view($this->data['article_id']);
        $data['list']['views'] = $view['views'];
        $this->apiReturn('0', '成功', $data);
    }

    /**
     * 给文章点赞
     * @param int article_id 文章ID
     * @return int status 查询状态（接口状态）
     * @return int code 查询结果码（0状态码）
     * @return array data  article_id表示文章ID
     * @return int article_id  article_id表示文章ID
     * @return int praise  article_id表示文章ID
     */
    public function praise()
    {
        $where = array();
        $where['status'] = array('eq', 1);
        //获取文章ID
        if (!isset($this->data['article_id']) || empty($this->data['article_id'])) {
            $this->apiReturn(60001, '文章ID不能为空');
        }
        $article = $this->data['article_id'];
        $where['article_id'] = array('eq', $article);
        $model = M('Article');
        // 检测文章是否存在
        $info = $model->field('article_id')->where($where)->find();
        if (empty($info)) {
            $this->apiReturn(60002, '文章不存在');
        }
        $result = $model->where($where)->setInc('praise');
        //获取当前文章被赞数量
        $value = $model->where($where)->field('praise')->find();
        $data = array(
            'article_id' => $article,
            'praise' => $value['praise'],
        );
        //判断执行结果
        if ($result) {
            $this->apiReturn(0, '点赞成功', $data);
        } else {
            $this->apiReturn(-1, '失败，非法请求');
        }


    }

    /**
     * 文章浏览量
     * @param int article_id 文章ID
     * @return int status 查询状态（接口状态）
     * @return int code 查询结果码（0状态码）
     * @return array data  article_id表示文章ID
     * @return int article_id  表示文章ID
     * @return int views  浏览量
     */
    private function view($id)
    {
        $where = array();
        $where['status'] = array('eq', 1);
        $article = $id;
        $where['article_id'] = array('eq', $article);
        $model = M('Article');
        $model->where($where)->setInc('views');
        //获取当前文章浏览数量
        $value = $model->where($where)->field('views')->find();
        if (false === $value) {
            $data['status'] = false;
            return $data;
        }
        $data['status'] = true;
        $data['views'] = $value['views'];
        return $data;
    }


    /**
     * 文章评论列表
     * @param string title 查询条件标题
     * @param string article_id 查询条件 文章/社区ID
     * @return array list 文章具体数据
     * @return int count 评论总数量
     * @return int page 当前页数
     * @return int status 查询状态（接口状态）
     * @return int code 查询结果码（0状态码）
     * @return array where 查询条件
     * @return int comment_id 评论ID
     * @return int article_id 文章ID
     * @return int user_id 评论者ID
     * @return string content 评论内容
     * @return int status 评论状态
     * @return int add_time 评论添加时间
     * @return int update_time 评论修改时间
     * @return string true_name 评论者的真实姓名
     */
    public function review_list()
    {
        //搜索
        $where = array();
        //过滤已删除的评论
        $where['c.status'] = array('eq', 1);
        /*搜索条件开始*/
        if ((isset($this->data['comment_id']) && !empty($this->data['comment_id'])) ||
            (isset($this->data['article_id']) && !empty($this->data['article_id']))
        ) {
            if ($this->data['comment_id']) {
                $where['c.comment_id|c.article_id'] = $this->data['comment_id'];
            } else {
                $where['c.comment_id|c.article_id'] = $this->data['article_id'];
            }
        }
        /*搜索条件结束*/

        /*分页开始*/
        //计算分页
        if (isset($this->data['page']) && intval($this->data['page']) > 0) {
            $this->page = intval($this->data['page']);
        }

        if (isset($this->data['page_size']) && intval($this->data['page_size']) > 0) {
            $this->page_size = intval($this->data['page_size']);
        }

        $limit = ($this->page - 1) * $this->page_size . ',' . $this->page_size;

        // 获取总条数
        $total = M('Comment')->alias('c')->where($where)->count();
        /*分页结束*/

        //查询数据库
        $lists = M('Comment')->alias('c')
            ->join('__USER__ AS u ON u.uid = c.user_id', 'LEFT')
            ->field('c.*,u.user_avatar')//(LY) 2015年12月18日11:49:03 添加用户头像
            ->where($where)
            ->order('c.article_id ASC')
            ->limit($limit)
            ->select();
        $data = array(
            'page' => $this->page,
            'count' => $total,
            'list' => $lists ? $lists : '',
        );
        $this->apiReturn(0, '成功！', $data);
    }


    /**
     * 添加文章评论
     * @param string content 评论内容
     * @param string article_id 文章ID
     * @param string user_id 评论者的ID
     * @return array list 文章具体数据
     * @return int count 评论总数量
     * @return int page 当前页数
     * @return tinyint status 查询状态（接口状态）
     * @return int code 查询结果码（0状态码）
     * @return array where 查询条件
     */
    public function review()
    {
        $user_id = $this->isLogin();
        if (!isset($this->data['content']) || empty($this->data['content'])) {
            $this->apiReturn(60003, '评论内容不能为空');
        }
        //获取敏感词汇内容
        $vocabulary = M('SensitiveVocabulary')->where(array('status' => 1))->field('vocabulary')->getField('vocabulary', true);
        //过滤字符实体
        $this->data['content'] = htmlspecialchars($this->data['content']);
        if (!empty($vocabulary)) {
            foreach ($vocabulary as $val) {
                $this->data['content'] = str_replace($val, '*', $this->data['content']);
            }
        }
        if (!isset($this->data['article_id']) && empty($this->data['article_id'])) {
            $msg = '文章不能为空';
            $this->apiReturn('002', $msg);
        }
        $data = array();
        $this->data['user_id'] = $user_id;
        $data['content'] = $this->data['content'];

        //添加时间
        $data['add_time'] = NOW_TIME;
        //修改时间
        $data['update_time'] = NOW_TIME;
        // 读取数据库配置文件
        $config = S('DB_CONFIG_DATA');
        if (!$config) {
            $config = api('Config/lists');
            S('DB_CONFIG_DATA', $config);
        }
        //获取评论审核开关
        $switch = $config['DB_COMMENT_SWITCH'];
        if ($switch) {
            $data['status'] = 2;
        } elseif ($switch == 0) {
            //添加状态  1正常 2待审核  -1 禁用（默认开启）
            $data['status'] = 1;
        }
        //保存数据
        $comment_id = M('Comment')->add($data);
        if ($comment_id === false) {
            $this->apiReturn(-1, '保存失败');
        }
        $this->apiReturn(0, '成功');
    }

}