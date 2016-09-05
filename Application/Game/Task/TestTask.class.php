<?php

namespace Game\Task;

/**
 * 此文件为测试文件，其它任务可仿照此文件书写
 * Class TestTask
 * @package Game\Logic
 */
class TestTask
{

    /**
     * 任务是否完成
     * @param array $task 当前任务详情
     * @return array|bool false-未完成， true-任务完成， array-任务进度array('csc=>'任务进度', 'remain_time'=>'剩余时间')
     */
    public function csc($task = array())
    {

        return array('csc' => 0, 'remain_time' => 0);
    }
}