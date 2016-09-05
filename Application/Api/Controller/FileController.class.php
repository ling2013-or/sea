<?php

namespace Api\Controller;

use Think\Upload;

/**
 * 文件图片管理
 * Class FileController
 * @package Api\Controller
 */
class FileController extends ApiController
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
     * 图片上传
     */
    public function picture()
    {
        // 图片上传支持类型
        $pic_type_arr = array('evaluate');
        if (!isset($this->data['type']) || !in_array(strtolower($this->data['type']), $pic_type_arr)) {
            $this->apiReturn(44001, '请选择图片上传类别');
        }

        if (!isset($_FILES['image'])) {
            $this->apiReturn(44002, '请选择要上传的图片');
        }

        $setting = C('PICTURE_UPLOAD');
        $setting['rootPath'] = $setting['rootPath'] . trim($this->data['type']) . '/';

        $Upload = new Upload($setting);

        $info = $Upload->upload($_FILES);

        if ($info) { //文件上传成功，记录文件信息
            $data = array();
            $return = array();
            foreach ($info as $val) {

                $path = substr($setting['rootPath'], 1) . $val['savepath'] . $val['savename']; //在模板里的url路径

                $return[] = array(
                    'url' => C('WEB_DOMAIN') . $path,
                );
            }

            if (count($return) == 1) {
                $return = $return[0];
            }

            $this->apiReturn(0, 'ok', $return);
        } else {
            $this->apiReturn(43003, $Upload->getError());
        }
    }
}