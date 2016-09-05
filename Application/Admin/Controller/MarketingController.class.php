<?php
namespace Admin\Controller;


/**
 * 营销
 * Class LoginController
 * @package Admin\Controller
 */
class MarketingController extends AdminController
{
	/**
	 * 消息列表
	 * @access pulic
	 * @return void
	 */
    public function news()
    {
        $where = array();
        $where['t1.status'] = array('neq',-1);
        //搜索
        if(isset($_GET['id'])){
            $where['t1.id'] = I('id');
        }
        //分页
        $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        //统计总数
        $total = M('Message')->alias('t1')->where($where)->count();
        //实例化分页类
        $page = new \Think\Page($total, $listRows);
        $p = $page->show();
        //每页显示数量个数
        $limit = $page->firstRow . ',' . $page->listRows;
        //获取标前缀
        $lists = M('Message')->alias('t1')
            ->join("__USER__ t2 ON t1.uid = t2.uid", 'LEFT')
            ->field('t1.*,t2.user_name')
            ->where($where)
            ->order('id DESC')
            ->limit($limit)
            ->select();

        $this->assign('page', $p ? $p : '');
        $this->assign('total', $total);
        $this->assign('lists', $lists);
        $this->meta_title = '消息列表';
        $this->display();
    }

    /**
     * 添加消息
     */
    public function addNews()
    {
        if(IS_POST){
            //实例化model
            $message = D('message');
            //验证数据是否正常
            if($message->create()){
                //将数据插入数据库
                $message->add();
                $this->success('消息添加成功！',U('news'));
            }else{
                $this->error($message->getError());
            }

        }
        //获取前台用户列表
        $where = array();
        $where['status'] = array('eq',0);
        $user = M('User')->where($where)->field('uid,user_name')->select();
        $this->assign('user',$user);
        $this->meta_title = '添加消息';
        $this->display();
    }

    /**
     * 编辑社区信息
     * @param string $id 社区唯一ID
     */
    public function editNews($id='')
    {
        if(IS_POST){
            //实例化一个model
            $message = D('message');
            //判断值得格式
            if($message->create()){
                //更新数据库
                $message->save();
                $this->success('修改成功',U('news'));
            }else{
                $this->error($message->getError());
            }
        }
        //通过article获取文章信息
        $article = D('message');
        $result = $article->lists();
        //获取前台用户列表
        $where = array();
        $where['status'] = array('eq',0);
        $user = M('User')->where($where)->field('uid,user_name')->select();
        $this->assign('user',$user);
        //设置标题
        $this->meta_title = '编辑消息';
        $this->assign('info',$result['0']);
        $this->display();
    }

    /**
     * 删除社区
     */
    public function delNews()
    {
        $id = array_unique((array)I('id', 0));
        if (empty($id)) {
            $this->error('请选择要操作的数据!');
        }

        $map = array('id' => array('in', $id));
        $data['status'] = -1;

        if (M('message')->where($map)->save($data)) {
            $this->success('删除成功');
        } else {
            $this->error(M('Article')->getError());
        }
    }



	/**
	 * 社区列表
	 */
	public function community()
	{

        $where = '';
        if (isset($_GET['query'])) {
                $query = I('query');
                $map['t1.title'] = array('like', '%'.$query);
                $map['t1.article_id'] = array('eq', $query);
                $map['_logic'] = 'or';
                $where['_complex'] = $map;

        }


        //分页
        $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        //统计总数
        $total = M('Article')->where($where)->alias('t1')->join("__ADMIN__ t2 ON t1.user_id = t2.admin_id",'LEFT')->count();
        //实例化分页类
        $page = new \Think\Page($total, $listRows);
        $p = $page->show();
        //每页显示数量个数
        $limit = $page->firstRow . ',' . $page->listRows;
        //获取标前缀
        $lists = M('Article')->alias('t1')
            ->join("__ADMIN__ t2 ON t1.user_id = t2.admin_id",'LEFT')
            ->field('t1.*,t2.true_name')
            ->where($where)
            ->order('t1.article_id ASC')
            ->limit($limit)
            ->select();

        $this->assign('page', $p ? $p : '');
        $this->assign('total', $total);
        $this->assign('lists', $lists);
        $this->meta_title = '社区列表';
        $this->display();
	}




    /**
     * 编辑社区信息
     * @param string $id 社区唯一ID
     */
    public function editCommunity($id='')
    {
        if(IS_POST){
            //实例化model
            $community = D('Article');
            //验证修改数据格式是否正常
            if($community->create()){
                //更新数据库
                $community->save();
                $this->success('修改成功',U('Community'));
            }else{
                $this->error($community->getError());
            }
        }
        //通过article获取文章信息
        $article = D('Article');
        $result = $article->lists();
        //设置标题
        $this->meta_title = '编辑文章';
        $this->assign('info',$result['0']);
        $this->display();
    }

    /**
     * 添加社区信息
     */
    public function addCommunity()
    {
        if(IS_POST){
            //获取接收到的值
           $article = D('Article');
            if($article->create()){
                //插入数据库
                $result = $article->add();
                if($result) $this->success('添加成功',U('Community'));
            }else{
                $this->error($article->getError());
            }

        }
        $this->meta_title = '添加文章';
        $this->display();
    }

    /**
     * 删除社区
     */
    public function delCommunity()
    {
        $id = array_unique((array)I('id', 0));
        if (empty($id)) {
            $this->error('请选择要操作的数据!');
        }

        $map = array('article_id' => array('in', $id));
        $data['status'] = -1;

        if (M('Article')->where($map)->save($data)) {
            $this->success('删除成功');
        } else {
            $this->error(M('Article')->getError());
        }
    }



    /**
     * 广告列表
     * @access pulic
     */
    public function advertisement()
    {
//    	echo "广告";die;
        $this->display();
    }


    /**
     * @param 文章评论回复管理
     */
    public function comment($val='')
    {
        //搜索
        $where = array();
        $where['t1.status'] = array('neq',-1);
        if (isset($_GET['query'])) {
            $query = I('query');
            $map['t1.comment_id'] = array('eq', $query);
            $map['t1.article_id'] = array('eq', $query);
            $map['_logic'] = 'or';
            $where['_complex'] = $map;

        }
        //分页
        $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        //统计总数
        $total = M('Comment')->alias('t1')->where($where)->count();
        //实例化分页类
        $page = new \Think\Page($total, $listRows);
        $p = $page->show();
        //每页显示数量个数
        $limit = $page->firstRow . ',' . $page->listRows;
        //查询数据库
        $lists = M('Comment')->alias('t1')
            ->join("__ADMIN__ t2 on t1.user_id = t2.admin_id",'LEFT')
            ->field('t1.*,t2.true_name')
            ->where($where)
            ->order('t1.article_id ASC')
            ->limit($limit)
            ->select();
        $this->assign('page', $p ? $p : '');
        $this->assign('total', $total);
        $this->assign('lists', $lists);
        $this->meta_title = '评论列表';
        $this->display();

    }


    /**
     * 编辑评论信息
     * @param string $id 社区唯一ID
     */
    public function editComment()
    {
        if(IS_POST){
            //实例化model
            $community = D('Comment');
            //验证修改数据格式是否正常
            if($community->create()){
                //更新数据库
                $community->save();
                $this->success('修改成功',U('Comment'));
            }else{
                $this->error($community->getError());
            }
        }
        //通过article获取文章信息
        $article = D('Comment');
        $result = $article->lists();

        //获取文章列表(状态正常的)
        $article = M('Article');
        $lists = $article->field('article_id,title')->where(array('status'=>array('eq',1)))->select();
        //设置标题
        $this->meta_title = '编辑评论';
        $this->assign('info',$result['0']);
        $this->assign('lists',$lists);
        $this->display();
    }

    /**
     * 添加评论信息
     */
    public function addComment()
    {
        if(IS_POST){
            //获取接收到的值
            $article = D('Comment');
            if($article->create()){
                //插入数据库
                $result = $article->add();
                if($result) $this->success('添加成功',U('Comment'));
            }else{
                $this->error($article->getError());
            }

        }
        //获取文章列表(状态正常的)
        $article = M('Article');
        $lists = $article->field('article_id,title')->where(array('status'=>array('eq',1)))->select();
        $this->assign('lists',$lists);
        $this->meta_title = '添加评论';
        $this->display();
    }

    /**
     * 删除社区
     */
    public function delcomment()
    {
        $id = array_unique((array)I('id', 0));
        if (empty($id)) {
            $this->error('请选择要操作的数据!');
        }

        $map = array('comment_id' => array('in', $id));
        $data['status'] = -1;

        if (false !== M('Comment')->where($map)->save($data)) {
            $this->success('删除成功');
        } else {
            $this->error(M('Comment')->getError());
        }
    }

    /**
     * 审核评论
     */
    public function commentStatus(){
        $id = array_unique((array)I('id', 0));
        if(empty($id)){
           $this->error('请选择要操作的数据');
        }

        $map = array('comment_id' => array('in', $id));
        $data['status'] = 1;

        if (false !== M('Comment')->where($map)->save($data)) {
            $this->success('审核通过');
        } else {
            $this->error(M('Comment')->getError());
        }
    }




}