<?php

namespace Api\Model;

use Think\Model;

/**
 * 订单产品评论处理业务
 * Class GoodsComment
 * @package Api\Model
 */
class GoodsCommentModel extends Model
{

    /**
     * 评论成功
     */
    const COMMENT_SUCCESS = 0;


    /**
     * 错误码
     * @var int
     */
    protected $code = 0;




    /**
     * 生成订单
     * @param  string $cart_ids 购物车ID（格式：1,2,3,4,5）
     * @param  int $uid 用户ID
     * @param  int $address_id 送货地址ID， 0-表示不配送，大于0-表述送货且是送货地址ID
     * @param  string $from 订单来源（Android, IOS, WeChat, AliPay）
     * @return bool|int
     */
    public function createComment($value = array())
    {

        $result = $this->addAll($value);
        if(!$result){
            $this->code = 43328;
            $this->error = '评论添加失败';
            return false;
        }
        return $result;
    }

    /**
     * @param $id 订单号码
     * @param string $status 所需要修改的订单状态
     * @return bool
     */
    public function updateState($id,$status = 'success'){
        $orderModel = D('Order');
        $result = $orderModel->updateOrder($id,$status);
        if(!$result['status']){
            $this->code = 43329;
            $this->error = $result['msg'];
            return false;
        }
        return true;

    }


}