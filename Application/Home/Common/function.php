<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/1
 * Time: 14:06
 */
function get_default_msg($code){
    $array = array(0=>'否',1=>'是');
    if(isset($array[$code])){
        return isset($array[$code]);
    }
}