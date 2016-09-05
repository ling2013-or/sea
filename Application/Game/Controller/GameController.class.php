<?php

namespace Game\Controller;

use Api\Controller\ApiController;

/**
 * 接口公共继承类
 * Class GameController
 * @package Game\Controller
 */
class GameController extends ApiController
{

    /**
     * 是否为好友农场
     * @var bool
     */
    protected $is_friend = false;

    /**
     * 会员农场详情
     * @var array
     */
    protected $farm_info = array();

    /**
     * 自己/好友农场ID
     * @var int
     */
    protected $owner_id = 0;

    /**
     * 好友农场详情
     * @var array
     */
    protected $owner_farm = array();

    /**
     * 初始化
     */
    protected function _initialize()
    {
        parent::_initialize();
//        $this->uid = $this->isLogin();

        /* 读取数据库中的配置 */
        $config = S('DB_CONFIG_DATA');
        if (!$config) {
            $config = api('GameConfig/lists');
            S('DB_GAME_CONFIG_DATA', $config);
        }
        C($config); //添加配置

        $farm_info = M('GameFarm')->where(array('user_id' => $this->uid))->find();
        if (!$farm_info) {
            $farm_info = $this->initFarm();
            if (!$farm_info) {
                $this->apiReturn(-1, '开通农场失败');
            }
            // TODO 调用位置
            $this->getUserInfo($this->uid);
        }

        $farm_info['status'] = json_decode($farm_info['status'], true);
        $this->farm_info = $farm_info;

        if (isset($this->data['owner_id']) && $this->uid != $this->data['owner_id']) {
            $this->owner_id = $this->data['owner_id'];
            $this->is_friend = true;
        } else {
            $this->owner_id = $this->uid;
        }

        // 检测是否为好友
        if ($this->is_friend) {
            $friend = M('HomeFriend')->where(array('uid' => $this->uid, 'fuid' => $this->owner_id))->find();
            if (!$friend) {
                $this->apiReturn(70001, '对方不是您好友');
            }

            // 检测是否已开通农场
            $owner_farm = M('GameFarm')->field(true)->where(array('user_id' => $this->owner_id))->find();
            if (!$owner_farm) {
                $this->apiReturn(70002, '对方尚未开通农场');
            }
            $owner_farm['status'] = json_decode($owner_farm['status'], true);
            $this->owner_farm = $owner_farm;
        } else {
            $this->owner_farm = $this->farm_info;
        }

        // TODO 获取用户详情
    }

    /**
     * 新用户初始化农场（开通农场）
     */
    protected function initFarm()
    {
        // 农场提示语
        $tips = array(
            'water_help' => C('GAME_TIPS_WATER_HELP'),
            'weed_help' => C('GAME_TIPS_WEED_HELP'),
            'pest_help' => C('GAME_TIPS_PEST_HELP'),
            'weed_bad' => C('GAME_TIPS_WEED_BAD'),
            'pest_bad' => C('GAME_TIPS_PEST_BAD'),
        );

        // 初始化农田参数 TODO
        $status = array(
            /**
             * crop_id  作物ID
             * crop_status  作物生长状态：0-未种植，1-新种植，6-收获期，7-枯萎
             * weed_num 杂草数量
             * pest_num 害虫数量
             * humidity   湿度1-正常，0-干旱
             * health   健康度
             * harvest_num  收获次数
             * output   产量
             * least_remain_output  最少剩余产量
             * remain_output    剩余产量
             * steal_record 被采摘（偷取）记录
             * fertilize    可施肥阶段
             * plant_time   种植时间
             * update_time  更新时间
             * land_type: 土地类型：0-普通土地，1-红土地，2-黑土地
             *
             */
            array('crop_id' => 2, 'crop_status' => 6, 'weed_num' => 0, 'pest_num' => 0, 'humidity' => 1, 'health' => 100, 'harvest_num' => 0, 'output' => 16, 'least_remain_output' => 9, 'remain_output' => 16, 'steal_record' => array(), 'fertilize' => 0, 'plant_time' => NOW_TIME, 'update_time' => NOW_TIME, 'land_type' => 0),
            array(
                'crop_id' => 2,
                'crop_status' => 6,
                'weed_num' => 0,
                'pest_num' => 0,
                'humidity' => 1,
                'health' => 100,
                'harvest_num' => 0,
                'output' => 16,
                'least_remain_output' => 9,
                'remain_output' => 16,
                'steal_record' => array(),
                'fertilize' => 0,
                'plant_time' => NOW_TIME,
                'update_time' => NOW_TIME,
                'land_type' => 0
            ),
            /**
             * a:作物ID
             * b:作物生长阶段：0-未种植，1-新种植，6-收获期，7-枯萎
             * c:
             * d:
             * e:
             * f:杂草数量
             * g:害虫数量
             * h:土地是否干旱
             * i:作物生长状态
             * j:已收获季数
             * k:产量
             * l:最少剩余产量
             * m:剩余产量
             * n: array(采摘人ID=>采摘数量)
             * o:下次施肥阶段
             * p:农作物状态（时间戳+1=>1(放虫)，时间戳+1=>2(种草)）
             * q:种植时间
             * r:农场操作请求时间 （更新时间）
             * bitmap: 土地类型：0-普通土地，1-红土地，2-黑土地
             * pId:
             */
//            array('a' => 2, 'b' => 6, 'c' => 0, 'd' => 0, 'e' => 1, 'f' => 0, 'g' => 0, 'h' => 1, 'i' => 100, 'j' => 0, 'k' => 16, 'l' => 9, 'm' => 16, 'n' => array(), 'o' => 0, 'p' => array(), 'q' => ($_QFG['timestamp'] - 36030), 'r' => 1251351720, 'bitma' => 0, 'pId' => 0),
//
//            array('a' => 2, 'b' => 1, 'c' => 0, 'd' => 0, 'e' => 1, 'f' => 1, 'g' => 0, 'h' => 1, 'i' => 100, 'j' => 0, 'k' => 0, 'l' => 0, 'm' => 0, 'n' => array(), 'o' => 0, 'p' => array(), 'q' => ($_QFG['timestamp'] - 14400), 'r' => 1251351725, 'bitma' => 0, 'pId' => 0),
//
//            array('a' => 2, 'b' => 1, 'c' => 0, 'd' => 0, 'e' => 1, 'f' => 0, 'g' => 0, 'h' => 0, 'i' => 100, 'j' => 0, 'k' => 0, 'l' => 0, 'm' => 0, 'n' => array(), 'o' => 0, 'p' => array(), 'q' => ($_QFG['timestamp'] - 14400), 'r' => 1251351725, 'bitma' => 0, 'pId' => 0),
//
//            array('a' => 2, 'b' => 1, 'c' => 0, 'd' => 0, 'e' => 1, 'f' => 0, 'g' => 2, 'h' => 1, 'i' => 100, 'j' => 0, 'k' => 0, 'l' => 0, 'm' => 0, 'n' => array(), 'o' => 0, 'p' => array(), 'q' => ($_QFG['timestamp'] - 25200), 'r' => 1251351725, 'bitma' => 0, 'pId' => 0),
//
//            array('a' => 0, 'b' => 0, 'c' => 0, 'd' => 0, 'e' => 1, 'f' => 0, 'g' => 0, 'h' => 1, 'i' => 100, 'j' => 0, 'k' => 0, 'l' => 0, 'm' => 0, 'n' => array(), 'o' => 0, 'p' => array(), 'q' => 0, 'r' => 1251351725, 'bitma' => 0, 'pId' => 0),
//
//            array('a' => 0, 'b' => 0, 'c' => 0, 'd' => 0, 'e' => 1, 'f' => 0, 'g' => 0, 'h' => 1, 'i' => 100, 'j' => 0, 'k' => 0, 'l' => 0, 'm' => 0, 'n' => array(), 'o' => 0, 'p' => array(), 'q' => 0, 'r' => 1251351725, 'bitma' => 0, 'pId' => 0)
        );

        $data = array(
            'user_id' => $this->uid,
            'status' => json_encode($status),
            'reclaim' => 0,
            'red_land' => 0,
            'exp' => C('GAME_INIT_FARM_EXP'),
            'dog' => '',
            'tips' => json_encode($tips),
            'bad_num' => 0,
            'tend_num' => C('GAME_TEND_NUM'),
        );
        $id = M('GameFarm')->add($data);
        if ($id) {
            $data['id'] = $id;
            return $data;
        } else {
            return false;
        }
    }

    /**
     * 获取用户信息
     * @param int $uid 自己/好友 用户ID
     * @param bool $is_friend 查询好友信息
     * @return array
     */
    protected function getUserInfo($uid, $is_friend = false)
    {
        $info = M('GameUser')->where(array('uid' => $uid))->find();
        if ($is_friend) {
            if (!$info) {
                // 初始化会员信息
                $info = array(
                    'uid' => $uid,
                    'money' => C('GAME_INIT_MONEY'),
                    'weather' => 1,
                    'visit_time' => NOW_TIME,
                );
                M('GameUser')->add($info);
            }
        }
        return $info;
    }
}