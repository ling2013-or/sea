<?php

namespace Game\Controller;

use Think\Controller;

/**
 * 计划任务，使用LINUX Crontab, 每天0点执行
 * Class CrontabController
 * @package Game\Controller
 */
class CrontabController extends Controller
{
    /**
     * 计划任务
     */
    public function cron()
    {
        /* 读取数据库中的配置 */
        $config = S('DB_CONFIG_DATA');
        if (!$config) {
            $config = api('GameConfig/lists');
            S('DB_GAME_CONFIG_DATA', $config);
        }
        C($config); //添加配置

        // 更新所有农场
        M('GameFarm')->where(1)->save(array('bad_num' => C('GAME_BAD_NUM'), 'tend_num' => C('GAME_TEND_NUM')));
    }
}