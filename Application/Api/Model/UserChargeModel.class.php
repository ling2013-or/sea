<?php

namespace Api\Model;

use Think\Model;

/**
 * 用户充值管理模型
 * Class UserChargeModel
 * @package Api\Model
 */
class UserChargeModel extends Model
{

    /**
     * 生成充值编号
     * @param int $uid 用户ID
     * @return string
     */
    public function makeSn($uid)
    {
        return mt_rand(10, 99)
        . sprintf('%010d', time() - 946656000)
        . sprintf('%03d', (float)microtime() * 1000)
        . sprintf('%03d', (int)$uid % 1000);
    }
}