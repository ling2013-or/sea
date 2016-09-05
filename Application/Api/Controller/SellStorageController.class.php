<?php
namespace Api\Controller;
use Think\Exception;

/**
 * 用户收益库存管理
 */
class SellorderController extends ApiController
{
    /**
     * 初始化
     * 检测会员是否登录
     */
    protected function _initialize()
    {
        parent::_initialize();

        $this->uid = $this->isLogin();
    }

    /**
     * 获取用户收益列表(兑换)
     */
    public function lists()
    {
        $m = M('SellOrderStorage');
        $map['status'] = 1;
        $map['user_id'] = $this->uid;

        //计算分页
        if (isset($this->data['page']) && intval($this->data['page']) > 0) {
            $this->page = intval($this->data['page']);
        }
        if (isset($this->data['page_size']) && intval($this->data['page_size']) > 0) {
            $this->page_size = intval($this->data['page_size']);
        }
        $limit = ($this->page - 1) * $this->page_size . ',' . $this->page_size;
        $count = $m->where($map)->count();
        $list = $m->where($map)->limit($limit)->order('id DESC')->select();
        if ($list) {
            $data = array(
                'page' => $this->page,
                'count' => $count,
                'list' => $list ? $list : '',
            );
            $this->apiReturn(0,'成功',$data);
        } else {
            $this->apiReturn(46801,'暂时不存在收益');
        }
    }

    /**
     * 用户收益
     *
     *id 要收益的库存ID
     */
    public function doCrop()
    {
        try {
            //开启事务
            $m = D('SellOrderStorage');
            $m->startTrans();

            $id = intval($this->data['id']);
            $res = $m->countCrop($this->uid,$id);
            if ($res===false) {
                throw new \Exception('无法获取库存信息',46801);
            } else if ($res === 0) {
                throw new \Exception('零收益',46802);
            }

            $d = D('UserStorage');
            if ($d->changeStorage($res['plan_id'],$res['total'])) {
                //修改用户收益状态
                $map['id'] = $id;
                $map['user_id'] = $this->uid;
                $data['status'] = 2;
                $result = $m->where($map)->save($data);
                if ($result) {
                    $m->commit();
                    $this->apiReturn(0,'成功');
                } else {
                    throw new \Exception('系统繁忙，请稍候重试',-1);
                }
            } else {
                $msg = $d->getError();
                $no = $d->getCode();
                throw new \Exception($msg,$no);
            }
        } catch(\Exception $e) {
            $m->rollback();
            $error_msg = $m->getError();
            $error_no = $m->getCode();
            throw new \Exception($error_msg,$error_no);
        }
    }

}