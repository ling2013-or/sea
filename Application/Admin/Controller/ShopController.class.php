<?php
namespace Admin\Controller;

use Think\Controller;
use Think\Page;

/**
 * 商场管理
 * Class ShopController
 * @package Admin\Controller
 */
class ShopController extends AdminController
{

    /**
     * 商品列表
     */
    public function index()
    {
        // 查询条件
        $map = array();
        $map['goods.status'] = array('NEQ', -1);

        /* 时间段查询 */
        if (isset($_GET['start_time'])) {
                $start_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/', $_GET['start_time']);
            $start_unixtime = $start_time ? strtotime($_GET['start_time']) : null;
            if ($start_unixtime) {
                $map['goods.add_time'][] = array('EGT', $start_unixtime);
            }
        }

        if (isset($_GET['end_time'])) {
            $end_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/', $_GET['end_time']);
            $end_unixtime = $end_time ? strtotime($_GET['end_time']) : null;
            if ($end_unixtime) {
                $map['goods.add_time'][] = array('LT', $end_unixtime + 86400);
            }
        }

        // 商品名
        $name = I('name', '', 'trim');
        if (!empty($name)) {
            $map['goods.name'] = array('LIKE', '%' . $name . '%');
        }


        //分页
        $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        $total = M('Goods')
            ->alias('goods')
            ->where($map)
            ->count();
        $page = new Page($total, $listRows);
        $p = $page->show();
        $limit = $page->firstRow . ',' . $page->listRows;

        $lists = M('Goods')
            ->alias('goods')
            ->field('goods.*')
            ->where($map)
            ->limit($limit)
            ->order("goods.id DESC")
            ->select();

        // 记录当前列表页的cookie
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->meta_title = '商品管理';
        $this->assign('page', $p ? $p : '');
        $this->assign('total', $total);
        $this->assign('lists', $lists);
        $this->display();
    }

    /**
     * 添加商品
     */
    public function add()
    {
        if (IS_POST) {
            $model = D('Goods');
            if ($model->create()) {
                if ($model->add()) {
                    $this->success('商品添加成功，即将返回商品列表！', cookie('__forward__'));
                } else {
                    $this->error('商品添加失败，请重新尝试！');
                }
            } else {
                $this->error($model->getError());
            }

        } else {
            //关联商品
            $plan_sells = M('Goods')->field('id,name')->where(array('status'=>array('neq',-1),'type'=>0))->select();
            $this->assign('goods_list', $plan_sells);

            $this->meta_title = '新增商品';
            $this->display();
        }
    }

    /**
     * 编辑商品
     */
    public function edit()
    {
        if (IS_POST) {
            $model = D('Goods');
            if ($model->create()) {
                if ($model->save() === false) {
                    $this->error('商品编辑失败，请重新尝试！');
                } else {
                    $this->success('商品编辑成功，即将返回商品列表！', cookie('__forward__'));
                }
            } else {
                $this->error($model->getError());
            }
        } else {
            $id = I('id', 0, 'intval');
            if (empty($id)) {
                $this->error('无法获取商品ID！');
            }

            $map['id'] = $id;
            $map['goods_status'] = array('neq', -1);
            $goods = M('goods')->where($map)->find();
            if (!$goods) {
                $this->error('商品不存在或已被删除！');
            }

            $goods['picture_more'] = json_decode($goods['picture_more']);
            //关联商品
            $plan_sells = M('Goods')->field('id,name')->where(array('status'=>array('neq',-1),'type'=>0))->select();
            $this->meta_title = '编辑商品';
            $this->assign('goods_list', $plan_sells);
            $this->assign('goods', $goods);
            $this->display();
        }
    }

    /**
     * 商品详情
     */
    public function details()
    {
        $id = I('id', 0, 'intval');
        if (empty($id)) {
            $this->error('无法获取商品ID！');
        }
        $map['goods.id'] = $id;
        $map['goods.goods_status'] = array('neq', -1);
        $goods = M('goods')->alias('goods')
            ->join('__TRANSPORT__ transport ON transport.id = goods.transport_id', 'LEFT')
            ->join('__PLAN_SELL__ plan ON plan.plan_id = goods.plan_id', 'LEFT')
            ->field('goods.*,plan.plan_name,transport.title')
            ->where($map)->find();
        if (!$goods) {
            $this->error('商品不存在或已被删除！');
        }

        $goods['goods_image_more'] = json_decode($goods['goods_image_more']);
        $this->meta_title = '编辑商品';
        $this->assign('goods', $goods);
        $this->display();
    }

    /**
     * 修改商品状态（上架|下架）
     */
    public function status()
    {
        $id = I('id', 0, 'intval');
        if (empty($id)) {
            $this->error('无法获取商品ID！');
        }

        $status = I('status', 0, 'intval');
        if ($status != 0) {
            $status = 1;
        }

        $data['id'] = $id;
        $data['status'] = $status;
        $res = M('Goods')->save($data);

        if (false === $res) {
            $this->error('状态修改失败！');
        } else {
            $this->success('状态修改成功！');
        }
    }

    /**
     * 删除商户
     */
    public function del()
    {
        $id = I('id', 0, 'intval');

        if (empty($id)) {
            $this->error('无法获取商品ID！');
        }

        // 商品删除只能删除平台自己的
        $res = M('Goods')->where(array('id' => $id, 'store_id' => 0))->save(array('status' => -1));
        if (false === $res) {
            $this->error('商品删除失败！');
        } else {
            $this->success('商品删除成功！');
        }
    }

    /**
     * 评论审核
     */
    public function access()
    {
        $id = I('id', 0, 'intval');

        if (empty($id)) {
            $this->error('无法获取评论ID！');
        }

        $status = I('status', 0, 'intval');
        if (!in_array($status, array(1, -2))) {
            $status = -2;
        }

        $data['id'] = $id;
        $data['status'] = $status;
        $res = M('GoodsComment')->save($data);
        if (false === $res) {
            $this->error('审核失败！');
        } else {
            $this->success('审核成功！');
        }
    }

    /**
     * 查看商品评论
     */
    public function comment()
    {
        $id = I('id', 0, 'intval');
        if (!$id) {
            $this->error('无法获取商品ID！');
        }
        $m = M('GoodsComment');
        $map = array();
        $map['comment.goods_id'] = $id;

        /* 时间段查询 */
        if (isset($_GET['start_time'])) {
            $start_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/', $_GET['start_time']);
            $start_unixtime = $start_time ? strtotime($_GET['start_time']) : null;
            if ($start_unixtime) {
                $map['comment.comment_time'][] = array('EGT', $start_unixtime);
            }
        }

        if (isset($_GET['end_time'])) {
            $end_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/', $_GET['end_time']);
            $end_unixtime = $end_time ? strtotime($_GET['end_time']) : null;
            if ($end_unixtime) {
                $map['comment.comment_time'][] = array('LT', $end_unixtime + 86400);
            }
        }

        //用户名搜索
        if (isset($_GET['user_name']) && $_GET['user_name'] !== '') {
            $map['user.user_name'] = array('LIKE', '%' . I('user_name', '') . '%');
        }

        //分页
        $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        $total = $m->alias('comment')
            ->join('__USER__ user ON user.uid = comment.user_id', 'LEFT')
            ->where($map)->count();
        $page = new Page($total, $listRows);
        $p = $page->show();
        $limit = $page->firstRow . ',' . $page->listRows;

        $lists = $m->alias('comment')
            ->join('__USER__ user ON user.uid = comment.user_id', 'LEFT')
            ->join('__ORDER__ orders ON orders.order_id = comment.order_id ', 'LEFT')
            ->join('__GOODS__ goods ON GOODS.id = comment.goods_id', 'LEFT')
            ->field('comment.*,goods.goods_name,user.user_name,orders.order_sn')
            ->where($map)->limit($limit)->select();

        foreach ($lists as &$value) {
            $value['comment_image'] = json_decode($value['comment_image']);
        }

        // 记录当前列表页的cookie
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->meta_title = '商品评论';

        $this->assign('page', $p ? $p : '');
        $this->assign('total', $total);
        $this->assign('lists', $lists);
        $this->display();

    }

    /**
     * 快捷回复
     */
    public function addreply()
    {
        if (IS_POST) {
            $id = I('id', 0, 'intval');
            if (!$id) {
                $this->error('无法获取评论ID');
            }

            $comment = I('comment_reply');
            if ($comment == '') {
                $this->error('回复内容不能为空');
            }

            $data['reply'] = $comment;
            $data['reply_time'] = NOW_TIME;
            $map['id'] = $id;
            $res = M('GoodsComment')->where($map)->save($data);
            if ($res === false) {
                $this->error('评论回复失败!');
            } else {
                $this->success('评论回复成功!');
            }
        } else {
            $id = I('id', 0, 'intval');
            $map['comment.id'] = $id;
            $info = M('GoodsComment')->alias('comment')
                ->join('__USER__ user ON user.uid = comment.user_id', 'LEFT')
                ->field('comment.id,comment.comment_time,comment.comment,user.user_name')
                ->where($map)
                ->find();
            $this->assign('info', $info);
            $this->display();
        }

    }

    /**
     * 快捷编辑
     */
    public function editreply()
    {
        if (IS_POST) {
            $id = I('id', 0, 'intval');
            if (!$id) {
                $this->error('无法获取评论ID');
            }

            $comment = I('comment_reply');
            if ($comment == '') {
                $this->error('回复内容不能为空');
            }

            $data['reply'] = $comment;
            $data['reply_time'] = NOW_TIME;
            $map['id'] = $id;
            $res = M('GoodsComment')->where($map)->save($data);
            if ($res === false) {
                $this->error('回复编辑失败!');
            } else {
                $this->success('回复编辑成功!');
            }
        } else {
            $id = I('id', 0, 'intval');
            $map['comment.id'] = $id;
            $info = M('GoodsComment')->alias('comment')
                ->join('__USER__ user ON user.uid = comment.user_id', 'LEFT')
                ->field('comment.id,comment.comment_time,comment.comment,comment.reply_time,comment.reply,user.user_name')
                ->where($map)
                ->find();
            $this->assign('info', $info);
            $this->display();
        }

    }
}