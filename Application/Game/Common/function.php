<?php

/**
 * 将级别转换成经验
 * @param int $level 农场级别
 * @return int
 */
function farm_level_to_exp($level)
{
    return intval(pow(($level + 0.5), 2) * 100 - 25);
}

/**
 * 将经验转换成级别
 * @param int $exp 农场经验
 * @return int
 */
function farm_exp_to_level($exp)
{
    return floor(sqrt(($exp + 25) / 100) - 0.5);
}

/**
 * 获取种子缓存信息
 * @return array
 */
function get_crop_cache()
{
    $seed = S('CROP');
    if (!$seed) {
        $seed = M('GameCrop')->cache('CROP')->getField('crop_id,crop_name,plant_level,buy_price,growth_cycle,harvest_num,expect_revenue,expect_output,exp,sell_price,gossip,depict,type,is_hidden,asset_id,status,one,two,three,four,five,six');
    }
    return $seed;
}