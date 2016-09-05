<?php

namespace Game\Controller;

/**
 * 好友管理
 * Class FriendController
 * @package Game\Controller
 */
class FriendController extends GameController
{
    /**
     * 好友列表
     */
    public function lists()
    {
        // 不分组显示全部好友
        $condition = array();
        if (isset($this->data['group'])) {
            $condition['gid'] = intval($this->data['group']);
        }

        // 按用户ID查找
        if (isset($this->data['uid']) && !empty($this->data['uid'])) {
            $condition['fuid'] = intval($this->data['fuid']);
        }

        // 按照用户名查找
        if (isset($this->data['name']) && !empty($this->data['name'])) {
            $condition['fusername'] = array('LIKE', '%' . trim($this->data['name']) . '%');
        }

        if (isset($this->data['page']) && intval($this->data['page']) > 0) {
            $this->page = intval($this->data['page']);
        }
        if (isset($this->data['page_size']) && intval($this->data['page_size']) > 0) {
            $this->page_size = intval($this->data['page_size']);
        }
        $limit = ($this->page - 1) * $this->page_size . ',' . $this->page_size;

        //获取总条数
        $count = M('HomeFriend')->where($condition)->count();
        $lists = M('HomeFriend')->field('uid,fuid,fusername,gid,create_time')->where($condition)->order('create_time DESC')->limit($limit)->select();

        $data = array(
            'page' => $this->page,
            'count' => $count,
            'lists' => $lists ? $lists : '',
        );
        $this->apiReturn(0, 'success', $data);
    }

    /**
     * 发出的邀请列表（不使用）
     */
    /*public function send()
    {
        $map['request.uid'] = $this->uid;

        //计算分页
        if (isset($this->data['page']) && intval($this->data['page']) > 0) {
            $this->page = intval($this->data['page']);
        }
        if (isset($this->data['page_size']) && intval($this->data['page_size']) > 0) {
            $this->page_size = intval($this->data['page_size']);
        }
        $limit = ($this->page - 1) * $this->page_size . ',' . $this->page_size;

        // 获取总条数
        $count = M('HomeFriendRequest')->alias('request')->where($map)->count();
        $list = M('HomeFriendRequest')->alias('request')
            ->join('__HOME_FRIEND_GROUP__ g ON request.gid=g.gid', 'LEFT')
            ->field('request.*,g.name gname')->where($map)->limit($limit)->select();
        //返回数据
        if ($list) {
            $data = array(
                'page' => $this->page,
                'count' => $count,
                'list' => $list ? $list : '',
            );
            $this->apiReturn(0,'成功',$data);
        } else {
            $this->apiReturn(72802,'未发出任何邀请');
        }
    }*/

    /**
     * 收到的邀请列表
     */
    public function request()
    {
        $map['request.fuid'] = $this->uid;

        //计算分页
        if (isset($this->data['page']) && intval($this->data['page']) > 0) {
            $this->page = intval($this->data['page']);
        }
        if (isset($this->data['page_size']) && intval($this->data['page_size']) > 0) {
            $this->page_size = intval($this->data['page_size']);
        }
        $limit = ($this->page - 1) * $this->page_size . ',' . $this->page_size;

        // 获取总条数
        $count = M('HomeFriendRequest')->alias('request')->where($map)->count();
        $list = M('HomeFriendRequest')->alias('request')
            ->join('__USER__ u ON request.uid=u.uid', 'LEFT')
            ->field('request.uid,request.fuid,request.create_time,request.remark,u.user_name uname')->where($map)->limit($limit)->select();
        //返回数据
        if ($list) {
            $data = array(
                'page' => $this->page,
                'count' => $count,
                'list' => $list ? $list : '',
            );
            $this->apiReturn(0,'成功',$data);
        } else {
            $this->apiReturn(72803,'未收到任何邀请');
        }
    }

    /**
     * 发出好友邀请
     *
     * 说明：
     * 参数 fuid 好友ID
     * 参数 gid 分组ID
     */
    public function send()
    {
        $fuid = intval($this->data['fuid']); // 要添加的好友ID
        $gid  = intval($this->data['gid']);  // 好友分组ID
        if ($gid == 0) $gid = 1; // 默认分组
        //获取用户名
        $map['status'] = 0;
        $map['fuid'] = $fuid;
        $fusername = M('User')->where($map)->getField('user_name');
        if ($fusername) {
            $data['fusername']   = $fusername;
            $data['uid']         = $this->uid;
            $data['fuid']        = $fuid;
            $data['gid']         = $gid;
            $data['create_time'] = NOW_TIME;
            //$data['remark']      = isset($this->data['remark']) ? $this->data['remark'] : '';
            $res = M('HomeFriendRequest')->add($data);
            if ($res) {
                $this->apiReturn(0, '成功');
            } else {
                $this->apiReturn(72806, '操作失败');
            }
        } else {
            $this->apiReturn(72807, '不存在该用户');
        }
    }

    /**
     * 同意好友邀请（添加好友）
     *
     * 说明：
     * 参数 fuid 好友ID
     * 参数 remark 好友昵称
     */
    public function agree()
    {
        $fuid = intval($this->data['fuid']);
        if (!$fuid) {
            $this->apiReturn(72804,'无法获取好友ID');
        }

        try {
            $m = M('HomeFriend');
            $m->startTrans();

            //查询是否存在好友申请
            $map['uid']  = $this->uid;
            $map['fuid'] = $fuid;
            $res = M('HomeFriendRequest')->where($map)->find();
            if (!$res) {
                throw new \Exception('未收到好友邀请', 72805);
            }

            //添加好友
            $data['uid']    = $this->uid;
            $data['fuid']   = $fuid;
            $data['gid']    = $res['gid'];
            $data['remark'] = isset($this->data['remark']) ? $this->data['remark'] : '';
            $data['fusername']   = $res['fusername'];
            $data['create_time'] = NOW_TIME;
            if (!$m->add($data)) {
                throw new \Exception('操作失败', 72806);
            }

            //删除好友申请
            if (!M('HomeFriendRequest')->where($map)->delete()) {
                throw new \Exception('操作失败', 72806);
            }

            //添加好友记录
            $map['action'] = 'add';
            $map['create_time'] = NOW_TIME;
            if (M('HomeFriendlog')->add($map)) {
                $m->commit();
                $this->apiReturn(0, '成功');
            } else {
                throw new \Exception('操作失败', 72806);
            }

        } catch (\Exception $e) {
            $m->rollback();
            $this->apiReturn($e->getCode(), $e->getMessage());
        }
    }

    /**
     * 拒绝好友邀请
     *
     * 说明：
     * 参数 fuid 好友ID
     */
    public function refuse()
    {
        $fuid = intval($this->data['fuid']);
        if (!$fuid) {
            $this->apiReturn(72804,'请选择要操作的好友邀请');
        }
        $map['uid']  = $this->uid;
        $map['fuid'] = $fuid;
        $res = M('HomeFriendRequest')->where($map)->delete();
        if ($res) {
            $this->apiReturn(0, '成功');
        } else {
            $this->apiReturn(72806, '操作失败');
        }
    }

    /**
     * 删除好友
     *
     * 说明：
     * 参数 fuid 好友ID
     */
    public function del()
    {
        $fuid = intval($this->data['fuid']);
        if (!$fuid) {
            $this->apiReturn(72804,'无法获取好友ID');
        }

        try {
            $m = M('HomeFriend');
            $m->startTrans();

            //删除好友
            $map['uid']  = $this->uid;
            $map['fuid'] = $fuid;
            if (!$m->where($map)->delete()) {
                throw new \Exception('操作失败', 72806);
            }

            //好友记录
            $map['action'] = 'delete';
            $map['create_time'] = NOW_TIME;
            if (M('HomeFriendlog')->add($map)) {
                $m->commit();
                $this->apiReturn(0, '成功');
            } else {
                throw new \Exception('操作失败', 72806);
            }
        } catch (\Exception $e) {
            $m->rollback();
            $this->apiReturn($e->getCode(), $e->getMessage());
        }
    }

    /**
     * 更改好友分组
     *
     * 说明：
     * 参数 gid 分组ID
     * 参数 fuid 好友ID
     */
    public function chgrp()
    {
        $gid  = intval($this->data['gid']);
        $fuid = intval($this->data['fuid']);
        if (!$fuid) {
            $this->apiReturn(72804,'无法获取好友ID');
        }
        if (!$gid) {
            $this->apiReturn(72808,'无法获取分组ID');
        }

        //验证分组是否为该用户
        $map['uid'] = $this->uid;
        $map['gid'] = $gid;
        $res = M('HomeFriendGroup')->where($map)->find();
        if (!$res) {
            $this->apiReturn(72809,'无法移动到当前组');
        }
        unset($map['gid']);
        $map['fuid'] = $fuid;
        $data['gid'] = $gid;
        $result = M('HomeFriend')->where($map)->save($data);
        if ($result === false) {
            $this->apiReturn(72806, '操作失败');
        } else {
            $this->apiReturn(0, '成功');
        }
    }

    /**
     * 更改好友备注
     *
     * 说明：
     * 参数 fuid 好友ID
     */
    public function remark()
    {
        $fuid = intval($this->data['fuid']);
        if (!$fuid) {
            $this->apiReturn(72804,'无法获取好友ID');
        }
        if ($this->data['remark'] == '') {
            $this->apiReturn(72810,'好友备注不能为空');
        }

        //验证分组是否为该用户
        $map['uid'] = $this->uid;
        $map['fuid'] = $fuid;
        $data['remark'] = $this->data['remark'];
        $res = M('HomeFriendGroup')->where($map)->save($data);
        if ($res === false) {
            $this->apiReturn(0, '成功');
        } else {
            $this->apiReturn(72806, '操作失败');
        }
    }

}