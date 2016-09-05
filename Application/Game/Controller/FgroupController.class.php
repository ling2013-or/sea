<?php

namespace Game\Controller;

/**
 * 好友分组管理
 * Class FgroupController
 * @package Game\Controller
 */
class FgroupController extends GameController
{
    /**
     * 获取好友分组
     *
     * 说明：
     * 存在默认分组，所以分组不会为空
     */
    public function lists()
    {
        $map['uid'] = $this->uid;
        $res = M('HomeFriend')->alias('f')
        	->join('__HOME_FRIEND_GROUP__ g ON g.gid=f.gid', 'LEFT')
        	->field('g.gid,g.name,count(f.gid) num')
        	->where($map)
            ->order('g.listorder')
        	->group('gid')
        	->select();
        
        $all['gid']  = 1;
        $all['name'] = '全部好友';
        $all['num']  = 0;
        foreach ($res as $value) {
        	$all['num'] += $value['num'];
        }
        array_unshift($res, $all);
        $this->apiReturn(0, '成功', $res);
    }

    /**
     * 创建分组
     *
     * 说明：
     *  groupname  群组名称
     *  listorder 群组排序ID
     */
    public function add()
    {
    	//查询最大排序
        $data['listorder'] = intval($this->data['index']);
        $data['name']      = empty($this->data['name']) ? '未命名' : $this->data['name'];
        $data['uid']       = $this->data['uid'];
        $gid = M('HomeFriendGroup')->add($data);
        if ($gid) {
            $data['gid'] = $gid;
            $this->apiReturn(0, '成功', $data);
        } else {
            $this->apiReturn(72810, '创建分组失败');
        }
    }

    /**
     * 编辑分组
     */
    public function edit()
    {
    	$map['uid']        = $this->uid;
        $map['gid']        = intval($this->data['gid']);
        $data['listorder'] = intval($this->data['index']);
        $data['name']      = empty($this->data['name']) ? '未命名' : $this->data['name'];
        if (M('HomeFriendGroup')->where($map)->save($data) === false) {
            $this->apiReturn(72811, '修改分组失败');
        } else {
            $this->apiReturn(0, '成功', $data);
        }
    }

    /**
     * 删除分组
     */
    public function del()
    {
        $map['gid'] = intval($this->data['gid']);
        $map['uid'] = $this->uid;

        //查询是否还有好友
        $res = M('HomeFriend')->where($map)->count();
        if ($res > 0) {
            $this->apiReturn(72812, '分组下仍有成员，不可删除');
        } else {

            if (in_array($map['gid'], array(1,2))) {
                $this->apiReturn(72813, '不可删除默认分组');
            }

            $res = M('HomeFriendGroup')->where($map)->delete();
            if ($res) {
                $this->apiReturn(0, '成功');
            } else {
                $this->apiReturn(72806, '未产生任何操作');
            }
        }
    }
}