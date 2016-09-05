<?php

namespace Api\Controller;

/**
 * 用户消息管理
 * @package Api\Controller
 */
class MessageController extends ApiController
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
     * 查看消息列表
     */
    public function lists()
    {
        $map['msg.to_id'] = array('IN', array(0, $this->uid));
        $map['msg.status'] = array('neq', -1);

        //系统消息
        $status = I('status', 0, 'intval');
        if ($status) {
            $map['sys.status'] = 1;
        } else {
            $child[]['sys.status'] = 0;
            $child[]['sys.status'] = array('EXP', 'IS NULL');
            $child['_logic'] = 'OR';
            $map[] = $child;
        }

        //查询条件 标题关键字
        if (isset($_GET['kw']) && $_GET['kw'] !== '') {
            $map['text.title'] = array('LIKE', '%' . I('kw') . '%');
        }

        //计算分页
        if (isset($this->data['page']) && intval($this->data['page']) > 0) {
            $this->page = intval($this->data['page']);
        }
        if (isset($this->data['page_size']) && intval($this->data['page_size']) > 0) {
            $this->page_size = intval($this->data['page_size']);
        }
        $limit = ($this->page - 1) * $this->page_size . ',' . $this->page_size;

        //获取总条数
        $count = M('MessageList')->alias('msg')
            ->join('__MESSAGE_TEXT__ text ON text.id=msg.text_id', 'LEFT')
            ->join('__MESSAGE_SYS__ sys ON sys.msg_id=msg.id', 'LEFT')
            ->where($map)->count();

        $lists =  M('MessageList')->alias('msg')
            ->join('__MESSAGE_TEXT__ text ON text.id=msg.text_id', 'LEFT')
            ->join('__MESSAGE_SYS__ sys ON sys.msg_id=msg.id', 'LEFT')
            ->field('msg.id,msg.add_time,msg.to_id,msg.status,text.title,text.content,sys.status is_sys')
            ->where($map)->order('msg.add_time DESC')->limit($limit)->select();
        //添加查看状态
        if ($lists) {
            $insert = array();
            foreach ($lists as $key=>$val) {
                if ($val['to_id'] == 0) {
                    if (is_null($val['is_sys'])) {
                        $data = array();
                        $data['msg_id'] = $val['id'];
                        $data['user_id'] = $this->uid;
                        $insert[] = $data;
                    } else {
                        $lists[$key]['status'] = $val['is_sys'];
                    }
                }
                unset($lists[$key]['is_sys']);

                $lists[$key]['add_time'] = date("Y-m-d H:i:s",$lists[$key]['add_time']);
            }
            M('MessageSys')->addAll($insert);
            $data = array(
                'page' => $this->page,
                'count' => $count,
                'list' => $lists ? $lists : '',
            );
            $this->apiReturn(0,'成功',$data);
        } else {
            $this->apiReturn(46501,'您没未读消息');
        }
    }

    /**
     * 读取消息
     */
    public function read()
    {
        $id = I('id', 0, 'intval');
        $map['msg.id'] = $id;
        $map['msg.to_id'] = array('IN', array(0, $this->uid));
        $info =  M('MessageList')->alias('msg')
            ->join('__MESSAGE_TEXT__ text ON text.id=msg.text_id', 'LEFT')
            ->join('__MESSAGE_SYS__ sys ON sys.msg_id=msg.id', 'LEFT')
            ->field('msg.id,msg.add_time,msg.to_id,msg.status,text.title,text.content,sys.status is_sys')
            ->where($map)->order('msg.add_time DESC')->find();

        $map = array();
        if ($info['to_id'] == 0) {
            $map['msg_id'] = $id;
            $map['user_id'] = $this->uid;
            M('MessageSys')->where($map)->save(array('status'=>1));
        } elseif ($info['to_id'] == $this->uid) {
            $map['id'] = $id;

            M('MessageList')->where($map)->save(array('status'=>1));
        }

        //添加查看状态
        if ($info) {
            $this->apiReturn(0,'成功',$info);
        } else {
            $this->apiReturn(46501,'您没未读消息');
        }
    }

    /**
     * 标识为已读
     */
    public function isread()
    {
        $id = I('id', 0, 'intval');
        if (!$id) {
            $this->apiReturn(46502,'无法获取消息ID');
        }

        $msg = M('MessageList')->find($id);
        if (!$msg) {
            $this->apiReturn(46503,'暂无消息');
        }

        if ($msg['to_id'] == 0) {
            $map['msg_id'] = $id;
            $map['user_id'] = $this->uid;
            $res = M('MessageSys')->where($map)->save(array('status'=>1));
        } elseif ($msg['to_id'] == $this->uid) {
            $map['id'] = $id;
            $res = M('MessageList')->where($map)->save(array('status'=>1));
        }
        if (isset($res) && $res !== false) {
            $this->apiReturn(0,'修改成功');
        } else {
            $this->apiReturn(46503,'修改失败');
        }

    }

    /**
     * 删除
     */
    public function del()
    {
        $id = I('id', 0, 'intval');
        if (!$id) {
            $this->apiReturn(46502,'无法获取消息ID');
        }

        $msg = M('MessageList')->find($id);
        if (!$msg) {
            $this->apiReturn(46503,'暂无消息');
        }

        if ($msg['to_id'] == 0) {
            $map['msg_id'] = $id;
            $map['user_id'] = $this->uid;
            $res = M('MessageSys')->where($map)->save(array('status'=>-1));
        } elseif ($msg['to_id'] == $this->uid) {
            $map['id'] = $id;
            $res = M('MessageList')->where($map)->save(array('status'=>-1));
        }
        if (isset($res) && $res !== false) {
            $this->apiReturn(0,'删除成功');
        } else {
            $this->apiReturn(46504,'删除失败');
        }
    }

}