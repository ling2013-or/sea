<?php
namespace Api\Controller;

/**
 * 短信发送
 * Class MarketController
 * @package Api\Controller
 */
class SmsController extends ApiController
{

    /**
     * 发送手机验证码消息
     * @param null $mobile 手机号码
     */
    public function send()
    {
        if(!isset($this->data['style']) && empty($this->data['style']) ){
            $this->apiReturn(50002,'参数不正确');
        }
        switch($this->data['style']){
            case 1:
                //发送给当前用户的手机号码
                $return = $this->mobile_my();
                if(!$return['status']){
                    $this ->apiReturn($return['code'],$return['msg']);
                }
                break;
            case 2:
                //发送给平台用户的手机号码（新用户绑定手机号码）
                $return = $this->mobile_send();
                if(!$return['status']){
                    $this ->apiReturn($return['code'],$return['msg']);
                }
                break;
            case 3:
                //给用户所更换的手机号码发送短信验证码
                $return = $this->mobile_update();
                if(!$return['status']){
                    $this ->apiReturn($return['code'],$return['msg']);
                }
                break;
            case 4:
                //新注册用户
                $return = $this->mobile_new();
                if(!$return['status']){
                    $this ->apiReturn($return['code'],$return['msg']);
                }
                break;
            default:
                $this->apiReturn(50006,'非法请求');
                break;

        }

        $mobile = $this->data['mobile'];
        //手机号码检测
        if (!isset($mobile) || empty($mobile)) {
            $this->apiReturn(50008, '手机号码不能为空');
        }
        //手机号码正则验证
        $res = preg_match("/^1\d{10}$/", $mobile);
        if (!$res) {
            $this->apiReturn(50009, '手机号码不合法');
        }

        //生成指定长度的code
        $code = code(6);
        //获取短信模板配置  code模板
        $where['alias'] = 'code';
        //获取模板信息
        $smsTpl = M('SmsTpl');
        $tpList = $smsTpl->where($where)->find();

        //模板参数
        $param = explode(',', $tpList['param']);

        //模板数据
        $example = $tpList['content'];
        $tpl = '';
        foreach ($param as $para) {
            if ($para == '[code]') {
                $tpl = str_replace($para, $code, $example);
            }
        }
        //组装信息内容
        $content = $tpl;
        //发送短信
        $result = sendsms($mobile, $content, '');
        if (!$result) {
            $this->apiReturn(50010, '发送失败,请重试');
        }
        if($this->data['style'] == 1 || $this->data['style'] == 2 || $this->data['style'] == 3){
            //将验证码跟手机号码加密
            $key = md5(time().rand(1,1000).$mobile.$code);

            //TODO 测试完成之后删除
            $data = array(
                'code' => $code,
                'key' => $key
            );
        }else{
            $key = 0;
            //TODO 测试完成之后删除
            $data = array(
                'code' => $code
            );
        }
        //记录验证码
        $this->memberCode($mobile, $code,$key, 'code');
        $this->apiReturn(0, '发送成功', $data);
    }

    /**
     * 给用户自己的手机号码发送短信
     */
    private function mobile_my(){
        $this->uid = $this->isLogin();
        $msg = array();
        //获取当前用户的手机号码
        $condition = array();
        $condition['uid'] = $this->uid;
        $Model = D('User');
        $user = $Model->field('user_phone')->where($condition)->find();
        $m = $user['user_phone'];
        if($m){
            $this->data['mobile'] = $m;
            $msg = array('status'=>true);
            return $msg;
        }else{
            $msg = array('status'=>false,'code'=>50001,'msg'=>'当前用户未设置手机号码');
            return $msg;

        }
    }

    /**
     * 新用户绑定手机号码
     */
    private function mobile_new(){
        //新手机号码不能是已存在的
        $msg = array();
        $user = M('User');
        $map = array();
        $map['user_phone'] = $this->data['mobile'];
        $check = $user->field('uid')->where($map)->find();
        if($check){
            $msg = array('status'=>false,'code'=>50007,'msg'=>'手机号码已存在！');
            return $msg;
        }else{
            $msg = array('status'=>true);
            return $msg;
        }
    }

    /**
     * 给平台用户发送短信验证码
     */
    private function mobile_send(){
        //检查号码当前手机号码是否存在
        $msg = array();
        $user = M('User');
        $map = array();
        $map['user_phone'] = $this->data['mobile'];
        $check = $user->field('uid')->where($map)->find();
        if(!$check){
            $msg = array('status'=>false,'code'=>50003,'msg'=>'此手机号码不存在');
            return $msg;
        }else{
            $msg = array('status'=>true);
            return $msg;
        }

    }

    /**
     * 给用户所更换的手机号码发送短信验证码
     */
    private function mobile_update(){
        $msg = array();
        if(!isset($this->data['key']) && empty($this->data['key'])){
            $msg = array('status'=>false,'code'=>50004,'msg'=>'用户没有验证原来的手机号码，非法请求');
            return $msg;
        }

        $key = base64_decode(trim($this->data['key']));
        //注意当初的小标已经消失。
        $if['key'] = $this->data['key'];
        $if['status'] = -1;
        $if['key_status'] = 1;
        //检测一个小时之内发送的短信验证码
        $if['time'] = array('egt',intval(NOW_TIME-3600));

        //查询当前手机号码是否发送过此条信息
        //修改验证码状态
        $check =  M('SmsCode')->field('id,mobile')->where($if)->find();
        if($check){
            //校验新手机号码是否存在
            $user = M('User');
            $map = array();
            $map['user_phone'] = $this->data['mobile'];
            $check2 = $user->field('uid')->where($map)->find();
            //修改验证码的状态
            $update =  M('SmsCode')->where($if)->save(array('key_status'=>-1));
            if($check2){
                $msg = array('status'=>false,'code'=>50005,'msg'=>'新绑定的手机号码已存在。');
                return $msg;
            }else{
                $msg = array('status'=>true);
                return $msg;
            }

        }else{
            $msg = array('status'=>false,'code'=>50004,'msg'=>'用户没有验证原来的手机号码/验证已过期');
            return $msg;
        }
    }



    /**
     * 记录验证码发送
     */
    private function memberCode($mobile, $code = '', $key,$type = 'code')
    {
        $data = array(
            'mobile' => $mobile,
            'code' => $code,
            'type' => $type,
            'key' => $key,
            'time' => NOW_TIME,
        );
        $model = M('SmsCode');
        $result = $model->add($data);
        //以前的验证码全部失效
        $where['id'] = array('neq', $result);
        $model->where($where)->save(array('satatus' => -1,'mobile'=>$mobile));
        if ($result) {
            $value = array('status' => true, 'code' => 0, 'msg' => '记录成功');
        } else {
            $value = array('status' => false, 'code' => 50011, 'msg' => '记录失败,请重新登记');
        }
        return $value;
    }

    /**
     * 修改验证码的状态
     * @param string $mobile 手机号码
     * @param int $id 验证码记录唯一ID
     * @param int $status 验证码状态  -1失效 1正常
     * @return array 返回修改结果
     */
    private function updateCode($mobile,$id='', $status = -1)
    {
        $where['mobile'] = $mobile;
        $value['status'] = $status;
        if($id){
            $value['id'] = array('neq',$id);
        }

        $model = M('SmsCode');
        //修改验证码状态
        $result = $model->where($where)->save($value);
        if (false === $result) {
            $data = array('status' => false, 'code' => 50012, 'msg' => '状态修改失败');
        } else {
            $data = array('status' => true, 'code' => 0, 'msg' => '修改成功');
        }
        return $data;
    }

}