<?php

namespace Game\Logic;

/**
 * 农场游戏任务逻辑处理
 * Class TaskLogic
 * @package Game\Logic
 */
class TaskLogic
{

    /**
     * 任务模型
     * @var object
     */
    protected $task_model;

    /**
     * 会员任务模型
     * @var object
     */
    protected $user_task_model;

    /**
     * 奖励信息
     * @var array
     */
    public $list_data = array();

    /**
     * 任务信息
     * @var array
     */
    public $task = array();

    /**
     * 错误码
     * @var int
     */
    protected $code = 0;

    /**
     * 错误信息
     * @var string
     */
    protected $error = '';

    /**
     * 初始化
     */
    public function __construct()
    {
        // 任务模型
        $this->task_model = M('GameTask');
        // 会员任务模型
        $this->user_task_model = M('GameUserTask');
    }

    /**
     * 任务列表
     * @param int $uid 用户UID
     * @param string $status 查看列表条件
     * @return array
     */
    public function lists($uid, $status)
    {
        $status = strtolower($status);
        $lists = $this->fetchAllByStatus($uid, $status);
        if (!$lists) {
            return false;
        }

        $task_list = $end_task_ids = array();
        // 奖励类型：经验 = 种子 = 道具 = 装饰 = 金币
        $exp = $seed = $tool = $decorat = $money = array();
        $num = 0;
        foreach ($lists as $task) {
            if ($status == 'new' || $status == 'canapply') {
                // 新任务或者可领取任务
                list($task['allow_apply'], $task['next_time']) = $this->checkNextPeriod($task);
                if (!$task['allow_apply']) continue;
            }
            $num++;
            // 奖励类型：0-经验，1-种子，2-道具，3-装饰，4-金币
            switch ($task['reward']) {
                case 0:
                    $exp[] = $task['prize'];
                    break;
                case 1:
                    $seed[] = $task['prize'];
                    break;
                case 2:
                    $tool[] = $task['prize'];
                    break;
                case 3:
                    $decorat[] = $task['prize'];
                    break;
                case 4:
                    $money[] = $task['prize'];
                    break;
            }

            if ($task['available'] == 1 && ($task['starttime'] > NOW_TIME || ($task['endtime'] && $task['endtime'] <= NOW_TIME))) {
                $end_task_ids[] = $task['task_id'];
            }

            if ($status == 'doing' && $task['csc'] < 100) {
                $class = parse_res_name(ucfirst($task['scriptname']), 'Task');
                if (!class_exists($class)) {
                    $this->code = -1;
                    $this->error = '系统任务不存在';
                    return false;
                }
                $class = new $class();
                if (!method_exists($class, 'csc')) {
                    $this->code = -1;
                    $this->error = '任务不存在';
                    return false;
                }
                $res = $class->csc($task);
                if ($res === true) {
                    // 任务完成
                    $task['csc'] = 100;
                    $save = array('csc' => $task['csc'], 'update_time' => NOW_TIME);
                } elseif ($res === false) {
                    // 任务失败
                    $save = array('status' => -1, 'update_time' => NOW_TIME);
                } else {
                    // 完成部分
                    $task['csc'] = floatval($res['csc']);
                    $save = array('csc' => $task['csc'], 'update_time' => NOW_TIME);
                }
                $this->user_task_model->where(array('user_id' => $uid, 'task_id' => $task['task_id']))->save($save);
            }

            if (in_array($status, array('done', 'failed')) && $task['period']) {
                list($task['allow_apply'], $task['next_time']) = $this->checkNextPeriod($task);
            }

            $task_list[] = $task;
        }

        // 种子
        if ($seed) {
            foreach (M('GameCrop')->field('crop_id,crop_name')->where(array('IN', $seed))->select() as $val) {
                $this->list_data[$val['crop_id']] = $val['crop_name'];
            }
        }

        // 道具
        if ($tool) {
            foreach (M('GameTools')->field('tool_id,name')->where(array('IN', $tool))->select() as $val) {
                $this->list_data[$val['tool_id']] = $val['name'];
            }
        }

        // 装饰
        if ($decorat) {
            foreach (M('GameDecorat')->field('id,name')->where(array('IN', $decorat))->select() as $val) {
                $this->list_data[$val['id']] = $val['name'];
            }
        }

        if ($end_task_ids) {
        }

        return $task_list;
    }

    /**
     * 申请任务
     * @param int $uid 用户UID
     * @param int $task_id 任务ID
     * @return bool
     */
    public function apply($uid, $task_id)
    {
        $this->task = $this->task_model->where(array('task_id' => $task_id))->find();
        if (!isset($this->task['available']) || $this->task['available'] != 1) {
            $this->code = 76111;
            $this->error = '该任务不存在或已被删除';
            return false;
        } elseif (($this->task['starttime'] && $this->task['starttime'] > NOW_TIME) || ($this->task['endtime'] && $this->task['endtime'] <= NOW_TIME)) {
            $this->code = 76112;
            $this->error = '该任务尚未上线或已下线';
            return false;
        }

        if ($this->task['relatedtaskid'] && !$this->user_task_model->where(array('user_id' => $uid, 'task_id' => $this->task['relatedtaskid'], 'status' => 1))) {
            $this->code = 76113;
            $this->error = '申请此任务需要先完成另一个任务';
            return false;
        } elseif (!$this->task['period'] && $this->user_task_model->where(array('user_id' => $uid, 'task_id' => $task_id))->find()) {
            $this->code = 76114;
            $this->error = '您已申请过此任务，请不要重复申请';
            return false;
        } elseif ($this->task['period']) {
            $user_task = $this->user_task_model->where(array('user_id' => $uid, 'task_id' => $task_id))->find();
            $user_task['period'] = $this->task['period'];
            $user_task['periodtype'] = $this->task['periodtype'];
            list($allow_apply) = $this->checkNextPeriod($user_task);
            if (!$allow_apply) {
                $this->code = 76115;
                $this->error = '本期您已申请过此任务，请下期再来';
                return false;
            }
        }

        // TODO 以后可根据特殊任务执行对应的任务信息

        $data = array(
            'user_id' => $uid,
            'user_name' => M('User')->where(array('uid' => $uid))->getField('user_name'),
            'task_id' => $task_id,
            'csc' => 0,
            'create_time' => NOW_TIME,
            'update_time' => NOW_TIME,
        );
        $this->user_task_model->add($data, array(), true);

        // 增加申请人数
        $this->task_model->where(array('task_id' => $task_id))->setInc('applicants');

        return true;
    }

    /**
     * 删除（取消）任务
     * @param int $uid 用户UID
     * @param int $task_id 任务ID
     * @return bool
     */
    public function delete($uid, $task_id)
    {
        $this->task = $this->task_model->where(array('task_id' => $task_id))->find();
        if (!isset($this->task['available']) || $this->task['available'] != 1) {
            $this->code = 76111;
            $this->error = '该任务不存在或已被删除';
            return false;
        }

        $user_task = $this->user_task_model->where(array('user_id' => $uid, 'task_id' => $task_id))->find();
        if (!isset($user_task['status']) || $user_task['status'] == 1) {
            $this->code = 76111;
            $this->error = '该任务不存在或已被删除';
            return false;
        }

        // TODO 以后可以增加删除特殊任务

        // 删除用户任务
        $this->user_task_model->where(array('user_id' => $uid, 'task_id' => $task_id))->delete();

        $this->task_model->where(array('task_id' => $task_id))->setDec('applicants');

        return true;
    }

    /**
     * 领取奖励
     * @param int $uid 用户UID
     * @param int $task_id 任务ID
     * @return bool
     */
    public function draw($uid, $task_id)
    {
        $condition = array();
        $condition['ut.user_id'] = $uid;
        $condition['ut.task_id'] = $task_id;
        $condition['t.available'] = 1;
        $this->task = $this->user_task_model
            ->alias('ut')
            ->join('__GAME_TASK__ AS t ON ut.task_id = t.task_id')
            ->field('t.*,ut.csc,ut.create_time,ut.update_time,ut.status')
            ->where($condition)
            ->find();
        if (!$this->task) {
            $this->code = 76111;
            $this->error = '该任务不存在或已被删除';
            return false;
        }
        if ($this->task['status'] != 0) {
            $this->code = 76116;
            $this->error = '不是进行中的任务';
            return false;
        }

        $class = parse_res_name(ucfirst($this->task['scriptname']), 'Task');
        if (!class_exists($class)) {
            $this->code = -1;
            $this->error = '系统任务不存在';
            return false;
        }
        $class = new $class();
        if (!method_exists($class, 'csc')) {
            $this->code = -1;
            $this->error = '任务不存在';
            return false;
        }

        $res = $class->csc($this->task);
        if ($res === true) {
            // 任务完成
            $reward = $this->reward($uid);
            if (!$reward) {
                $this->code = 76121;
                $this->error = '系统错误';
                return false;
            }
            // TODO 通知消息
            $save = array('csc' => 100, 'update_time' => NOW_TIME, 'status' => 1, 'create_time' => NOW_TIME);
            $this->user_task_model->where(array('user_id' => $uid, 'task_id' => $task_id))->save($save);
            $this->task_model->where(array('task_id' => $task_id))->setInc('achievers');
            return true;
        } elseif ($res === false) {
            // 任务失败
            $save = array('status' => -1, 'update_time' => NOW_TIME);
            $this->user_task_model->where(array('user_id' => $uid, 'task_id' => $task_id))->save($save);
            $this->code = 76122;
            $this->error = '您没能在指定时间内完成任务';
            return false;
        } else {
            // 完成一部分
            if (isset($res['csc'])) {
                $save = array('csc' => $res['csc'], 'update_time' => NOW_TIME);
                $this->user_task_model->where(array('user_id' => $uid, 'task_id' => $task_id))->save($save);
            }
            return $res;
        }
    }

    /**
     * 放弃任务
     * @param int $uid 用户UID
     * @param int $task_id 任务ID
     * @return bool
     */
    public function giveup($uid, $task_id)
    {
        $condition = array();
        $condition['ut.user_id'] = $uid;
        $condition['ut.task_id'] = $task_id;
        $condition['t.available'] = 1;
        $this->task = $this->user_task_model
            ->alias('ut')
            ->join('__GAME_TASK__ AS t ON ut.task_id = t.task_id')
            ->field('t.*,ut.csc,ut.create_time,ut.update_time,ut.status')
            ->where($condition)
            ->find();
        if (!$this->task) {
            $this->code = 76111;
            $this->error = '该任务不存在或已被删除';
            return false;
        }
        if ($this->task['status'] != 0) {
            $this->code = 76116;
            $this->error = '不是进行中的任务';
            return false;
        }

        // 删除用户任务
        $this->user_task_model->where(array('user_id' => $uid, 'task_id' => $task_id))->delete();

        $this->task_model->where(array('task_id' => $task_id))->setDec('applicants');

        return true;
    }

    /**
     * 检测下一时间段
     * @param array $task 任务信息
     * @return array
     */
    protected function checkNextPeriod($task)
    {
        $allow_apply = false;
        $next_time = '';
        $task['create_time'] = isset($task['create_time']) ? $task['create_time'] : 0;
        if ($task['periodtype'] == 0) {
            // 任务间隔周期：小时
            $allow_apply = NOW_TIME - $task['create_time'] >= $task['preiod'] * 3600 ? true : false;
            $next_time = $task['preiod'] * 3600 - NOW_TIME + $task['create_time'];
        } elseif ($task['periodtype'] == 1) {
            // 任务间隔周期：天
            $today_time = strtotime('today');
            $allow_apply = $task['create_time'] < $today_time - ($task['period'] - 1) * 86400 ? true : false;
            $next_time = $task['create_time'] + $task['period'] * 86400;
        } elseif ($task['periodtype'] == 2 && $task['period'] > 0 && $task['period'] <= 7) {
            // 任务间隔周期：周
            $task['period'] = $task['period'] != 7 ? $task['period'] : 0;
            $today_time = strtotime('today');
            $today_week = date('w');
            $week_start = $today_time - ($today_week - $task['period']) * 86400;    // 任务开始周期
            $this_week = $week_start - $task['period'] * 86400;                     // 本周第一天
            if ($task['create_time'] && ($task['create_time'] > $week_start || $task['create_time'] > $this_week)) {
                $allow_apply = false;
                $next_time = $task['create_time'] > $this_week ? $week_start + 604800 : $week_start;
            } else {
                $allow_apply = true;
            }
        } elseif ($task['periodtype'] == 3 && $task['period'] > 0) {
            // 任务间隔周期：月
            list($year, $month) = explode('-', date('Y-n', NOW_TIME));
            $month_start = mktime(0, 0, 0, $month, $task['preiod'], $year); // 本月任务开始时间
            $this_month = mktime(0, 0, 0, $month, 1, $year);        // 本月第一天
            if ($task['create_time'] && ($task['create_time'] > $month_start || $task['create_time'] > $this_month)) {
                $allow_apply = false;
                $next_time = $task['create_time'] > $this_month ? $month_start + 604800 : $month_start;
            } else {
                $allow_apply = true;
            }
        }
        return array($allow_apply, $next_time);
    }

    /**
     * 根据状态获取全部任务
     * @param int $uid 当前登录用户UID
     * @param string $status 查询状态
     * @return mixed
     */
    protected function fetchAllByStatus($uid, $status)
    {
        $condition = array();
        $condition['task.available'] = 1;
        $condition['utask.user_id'] = $uid;
        switch ($status) {
            case 'doing':
                $condition['utask.status'] = 0;
                break;
            case 'done':
                $condition['utask.status'] = 1;
                break;
            case 'failed':
                $condition['utask.status'] = -1;
                break;
            case 'canapply':
            case 'new':
            default:
                $condition['task.starttime'] = array('LT', NOW_TIME);
                $condition['_string'] = 'utask.task_id IS NULL OR (ABS(utask.status) = 1 AND task.period > 0)';
                break;
        }

        $lists = $this->task_model
            ->alias('task')
            ->join('__GAME_USER_TASK__ AS utask ON task.task_id = utask.task_id AND utask.user_id = ' . $uid, 'LEFT')
            ->field('task.*,utask.csc,utask.create_time,utask.update_time')
            ->where($condition)
            ->order('task.displayorder,task.task_id DESC')
            ->select();
        return $lists;
    }

    /**
     * 任务奖励
     * @param int $uid 当前登录用户UID
     * @return bool
     */
    public function reward($uid)
    {
        // TODO 日志记录
        $res = false;
        switch ($this->task['reward']) {
            case 0:     // 经验
                $res = M('GameFarm')->where(array('user_id' => $uid))->setInc('exp', $this->task['bonus']);
                break;
            case 1:     // 种子
                $seed = M('GameUserSeed')->where(array('user_id' => $uid, 'crop_id' => $this->task['prize']))->find();
                if ($seed) {
                    $res = M('GameUserSeed')->where(array('id' => $seed['id']))->setInc('amount', $this->task['bonus']);
                } else {
                    $res = M('GameUserSeed')->add(array('user_id' => $uid, 'crop_id' => $this->task['prize'], 'is_lock' => 0, 'amount' => $this->task['bonus']));
                }
                break;
            case 2:     // 道具
                $condition = array();
                $condition['tool_id'] = $this->task['prize'];
                $info = M('GameCrop')->where($condition)->find();

                if ($info['type'] == 1) {
                    // 化肥
                    // 更新用户数据库
                    $seed = M('GameUserTools')->where(array('user_id' => $uid, 'tool_id' => $this->task['prize']))->find();
                    if ($seed) {
                        $res = M('GameUserSeed')->where(array('id' => $seed['id']))->setInc('num', $this->task['bonus']);
                    } else {
                        $res = M('GameUserSeed')->add(array('user_id' => $uid, 'tool_id' => $this->task['prize'], 'num' => $this->task['bonus']));
                    }

                } elseif ($info['type'] == 2) {
                    $dog_time = M('GameFarm')->where(array('user_id' => $uid))->getField('dog');
                    // 狗粮
                    $effect_time = $info['effect_time'] * $this->task['bonus'];

                    $dog_feed_time = $dog_time < NOW_TIME ? (NOW_TIME + $effect_time) : ($dog_time + $effect_time);

                    // 更新狗粮时间
                    $res = M('GameFarm')->where(array('user_id' => $uid))->save(array('dog' => $dog_feed_time));

                } elseif ($info['type'] == 3) {
                    $dog_time = M('GameFarm')->where(array('user_id' => $uid))->getField('dog');
                    // TODO 狗狗有效期（以后再说）
                    // 狗狗   赠送一只
                    // 检测是否已经购买过此狗
                    if (!M('GameUserGog')->where(array('user_id' => $uid, 'tool_id' => $this->task['prize']))->find()) {
                        // 取消以默认狗狗
                        M('GameUserGog')->where(array('user_id' => $uid))->save(array('status' => 0));
                        $save = array();
                        $save['tool_id'] = $this->task['prize'];
                        $save['user_id'] = $uid;
                        $save['status'] = 1;
                        $res = M('GameUserGog')->add($save);
                    }

                    // 处理狗粮
                    $effect_time = (int)C('GAME_BUY_DOG_FEED_TIME');
                    if ($effect_time > 0) {
                        $dog_feed_time = $dog_time < NOW_TIME ? (NOW_TIME + $effect_time) : ($dog_time + $effect_time);
                        // 更新狗粮时间
                        $res = M('GameFarm')->where(array('user_id' => $uid))->save(array('dog' => $dog_feed_time));
                    }
                }
                break;
            case 3:     // 装饰
                // TODO 装饰
                break;
            case 4:     // 金币
                $res = M('GameUser')->where(array('uid' => $uid))->setInc('money', $this->task['bonus']);
                break;
        }
        return $res === false ? false : true;
    }
}