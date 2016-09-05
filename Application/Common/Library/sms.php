<?php
class Sms {
    /**
     * 发送手机短信
     * @param unknown $mobile 手机号
     * @param unknown $content 短信内容
     */
    public function send($mobile,$content) {
        return $this->duanxin($mobile,$content);
    }


    /*
     * duanxinwang.cc
     * 短信接口
     */

    private function duanxin($mobile,$content){
        header("Content-Type: text/html; charset=UTF-8");

        $flag = 0;
        $params='';//要post的数据
        $verify = rand(123456, 999999);//获取随机验证码

        //以下信息自己填以下
        //$mobile='18539296650';//手机号
        $argv = array(
            'name'=>'dxwysdiy',     //必填参数。用户账号
            'pwd'=>'A2C34A3D410D8153712DB7A61129',     //必填参数。（web平台：基本资料中的接口密码）
            'content'=>$content,   //必填参数。发送内容（1-500 个汉字）UTF-8编码
            'mobile'=>$mobile,   //必填参数。手机号码。多个以英文逗号隔开
            'stime'=>'',   //可选参数。发送时间，填写时已填写的时间发送，不填时为当前时间发送
//            'sign'=>'一品农夫',    //必填参数。用户签名。
            'sign'=>'艺鼠网',    //必填参数。用户签名。
            'type'=>'pt',  //必填参数。固定值 pt
            'extno'=>''    //可选参数，扩展码，用户定义扩展码，只能为数字
        );
        //print_r($argv);exit;
        //构造要post的字符串
        //echo $argv['content'];
        foreach ($argv as $key=>$value) {
            if ($flag!=0) {
                $params .= "&";
                $flag = 1;
            }
            $params.= $key."="; $params.= urlencode($value);// urlencode($value);
            $flag = 1;
        }
        $url = "http://web.duanxinwang.cc/asmx/smsservice.aspx?".$params; //提交的url地址
        $con= substr( file_get_contents($url), 0, 1 );  //获取信息发送后的状态

        if($con == '0'){
            return true;
        }else{
            echo "<script>alert('发送失败!');history.back();</script>";
        }
    }


}
