<?php

namespace Common\Api;

/**
 * 站点配置管理接口
 * Class ConfigApi
 * @package Common\Api
 */
class ConfigApi
{
    /**
     * 获取数据库中的配置列表
     * @array   array   配置数组
     */
    public static function lists()
    {
        $condition = array('status' => 1);
        $data = M('Config')->where($condition)->field('type,name,value')->select();

        $config = array();
        if ($data && is_array($data)) {
            foreach ($data as $val) {
                $config[$val['name']] = self::parse($val['type'], $val['value']);
            }
        }
        return $config;
    }

    /**
     * 根据配置类型解析配置
     * @param   int $type 配置类型
     * @param   string $value 配置值
     * @return  array
     */
    private static function parse($type, $value)
    {
        switch ($type) {
            case 3:     // 解析数组
                $array = preg_split('/[,;\r\n]+/', trim($value, ",;\r\n"));
                if (strpos($value, ':')) {
                    $value = array();
                    foreach ($array as $val) {
                        list($k, $v) = explode(':', $val);
                        $value[$k] = $v;
                    }
                } else {
                    $value = $array;
                }
                break;
        }
        return $value;
    }
}