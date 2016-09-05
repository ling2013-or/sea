<?php
namespace Api\Controller;

/**
 * 销售方案购物车管理
 */
class SellcartController extends ApiController
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
	 * 查询用户销售方案购物车列表
	 *
	 * 说明：
	 * 1、name 方案名称[搜索条件]
	 * 2、sn   方案编号[搜索条件]
	 */
	public function lists()
	{
		//查询条件
		$cart = M('SellCart');
		$map['cart.status']  = 1;
		$map['cart.user_id'] = $this->uid;

		//通过方案名称查询
		if (isset($this->data['name']) && $this->data['name'] !== '') {
			$map['plan.plan_name'] = array('LIKE','%' . $this->data['name'] . '%');
		}

		//通过方案编号查询
		if (isset($this->data['sn']) && $this->data['sn'] !== '') {
			$map['plan.plan_sn'] = $this->data['sn'];
		}

		//查询购物车列表
		$res = $cart->alias('cart')
			->join('__PLAN_SELL__ plan ON plan.plan_id=cart.plan_id','LEFT')
			->join('__PLAN_STORAGE__ storage ON storage.storage_id=cart.storage_id','LEFT')
			->field('storage.storage_price,storage.storage_id,storage.storage_name,cart.cart_id,cart.seed_img,cart.plan_id,cart.area,cart.update_time,plan.plan_price,plan.plan_name,plan.plan_descript')
			->where($map)->select();
		//购物车信息
		$result['total_num']   = 0;
		$result['total_money'] = 0;
		$result['list'] = array();
		if ($res) {
			foreach ($res as $k=>$row) {
				$state = $this->stateCheck($row['plan_id'], $row['storage_id'], $row['area']);
				if ($state['status']) {
					$total = $row['area']*$row['plan_price'];
					$res[$k]['cart_status'] = 1;
					$res[$k]['cart_price']  = $total;
					$res[$k]['total_money'] = $total + $row['storage_price'];
					$result['total_money'] += $res[$k]['total_money'];
				} else {
					//待删除的无效购物车
					//$destroy[] = $row['cart_id'];

					$res[$k]['cart_status'] = 0;
					$res[$k]['message'] = $state['msg'];
				}
			}
			$result['total_num'] = count($res);
			$result['list'] = $res;

			//删除无效购物车
			/*if (isset($destroy)) {
				$maps['cart_id'] = array('IN',$destroy);
				$data['status'] = -1;
				$cart->where($maps)->save($data);
			}*/

			$this->apiReturn(0,'成功',$result);
		} else {
			$this->apiReturn(46401,'暂无购物车信息',$result);
		}
	}

	/**
	 * 添加到购物车
	 */
	public function add()
	{
		$m = M('SellCart');
		$plan_id 	= intval($this->data['plan_id']); // 销售方案ID
		$storage_id = intval($this->data['storage_id']); // 存储方案ID
		$area 		= round(floatval($this->data['area']),2); // 购买面积
		$area_total = $area;

		//检测自已经购买当前方案的总面积
		$maps['plan_id'] = $plan_id;
		$maps['user_id'] = $this->uid;
		$maps['status']  = 1;
		$res = $m->field('area')->where($maps)->select();
		if ($res) {
			foreach ($res as $v) {
				$area_total += $v['area'];
			}
		}

		//方案检测
		$state = $this->stateCheck($plan_id, $storage_id, $area_total);
		if (!$state['status']) {
			$this->apiReturn(46403, $state['msg'], $state['data']);
		}

		//购物车操作
		$map['status'] 		= 1;
		$map['user_id'] 	= $this->uid;
		$map['plan_id'] 	= $plan_id;
		$map['storage_id']  = $storage_id;
		$cart_info = $m->where($map)->find();
		if ($cart_info) {
			//合并购物车
			$data['area'] 		 = $cart_info['area'] + $area;
			$data['cart_id'] 	 = $cart_info['cart_id'];
			$data['update_time'] = NOW_TIME;
			if (!$m->save($data)) {
				$this->apiReturn(-1,'系统繁忙，请稍候重试');
			}
			$cart_id = $cart_info['cart_id'];
		} else {
			//获取一张种子图片
			$seed = M('PlanSell')->field('seed_id')->find($plan_id);
			$imgs = M('Seed')->field('seed_img')->find($seed['seed_id']);
			$img_array = json_decode($imgs['seed_img']);

			//新增购物车
			$data['user_id'] 	= $this->uid;
			$data['seed_img']   = empty($img_array)?'':$img_array[0];
			$data['plan_id'] 	= $plan_id;
			$data['storage_id'] = $storage_id;
			$data['add_time'] 	= $data['update_time'] = NOW_TIME;
			$data['area'] 		= $area;
			$cart_id = $m->add($data);
			if (!$cart_id) {
				$this->apiReturn(-1,'系统繁忙，请稍候重试');
			}
		}

		//返回最新购物车信息
		$cart = $this->cartcount(true);
		$cart['cart_id'] = $cart_id;
		$this->apiReturn(0,'成功',$cart);
	}

	/**
	 * 修改购物车
	 */
	public function edit()
	{
		$cart_id 	= intval($this->data['cart_id']);
		$area 		= round(floatval($this->data['area']),2);

		//修改购物车
		$m = M('SellCart');
		$map['status'] = 1;
		$map['user_id'] = $this->uid;
		$map['cart_id'] = $cart_id;
		$cart_info = $m->where($map)->find();
		if ($cart_info) {

			//方案检测
			$state = $this->stateCheck($cart_info['plan_id'], $cart_info['storage_id'], $area);
			if (!$state['status']) {
				$result = $this->cartcount(true);
				$result['num'] =  $state['data'];
				$this->apiReturn(46403, $state['msg'], $result);
			}

			//修改购物车
			$data['area'] = $area;
			$data['update_time'] = NOW_TIME;
			$res = $m->where($map)->save($data);
			if ($res) {
				//统计购物车
				$result = $this->cartcount(true);
				$result['num'] =  $area;
				$this->apiReturn(0,'成功', $result);
			} else {
				$this->apiReturn(-1, '系统繁忙，请稍候重试');
			}
		} else {
			$this->apiReturn(46405,'提交的购物车不合法');
		}
	}

	/**
	 * 从购物车中删除
	 */
	public function del()
	{
		//验证数据
		$map['user_id'] = $this->uid;
		$map['cart_id'] = intval($this->data['id']);
		$data['status'] = -1;
		$data['update_time'] = NOW_TIME;
		$res = M('SellCart')->where($map)->save($data);
		if ($res) {
            $result = $this->cartcount(true);
			$this->apiReturn(0,'成功', $result);
		} else {
			$this->apiReturn(-1, '系统繁忙，请稍候重试');
		}
	}

	/**
	 * 清空购物车信息
	 */
	public function clear()
	{
	    //清空操作
	    $map['user_id'] = $this->uid;
	    $data['status'] = -1;
	    $data['update_time'] = NOW_TIME;
	    $res = M('SellCart')->where($map)->save($data);
	    if ($res) {
           //返回购物车的总价格，总数量
            $cart = array();
            $cart['total_num'] = 0;
            $cart['total_money'] = 0;
			$this->apiReturn(0,'成功',$cart);
	    } else {
			$this->apiReturn(-1, '系统繁忙，请稍候重试');
	    }
	}

	/**
	 * 确认订单
	 */
	public function confirm()
	{
		//开启事务
		$m = M('SellCart');

		//获取提交的数据
		$lists = array_unique((array)$this->data['cart_list']);
		if (empty($lists)) {
			$this->apiReturn(46404,'购物车为空');
		}

		//查询购物车中的订单信息
		$map['cart.status'] = 1;
		$map['cart.user_id'] = $this->uid;
		$map['cart.cart_id'] = array('IN',$lists);

		//查询要创建订单的购物车信息
		$cart_info = $m->alias('cart')
			->field('cart.cart_id,cart.plan_id,cart.storage_id,storage.storage_name,cart.area,cart.cart_id,storage.storage_price,plan.plan_price,plan.plan_name,plan.income_expect')
			->join('__PLAN_SELL__ plan ON plan.plan_id=cart.plan_id','LEFT')
			->join('__PLAN_STORAGE__ storage ON storage.storage_id=cart.storage_id','LEFT')
			->where($map)->select();
		if (!$cart_info) {
			$this->apiReturn(46405,'提交的购物车不合法');
		}

		//购物车统计
		$result['total_money'] = 0;
		$result['order_list'] = array();
		$cart_ids = array();
		foreach ($cart_info as $k=>$row) {
			//检验购物车状态
			$state = $this->stateCheck($row['plan_id'], $row['storage_id'], $row['area']);
			if ($state['status']) {
				$cart_ids[] = $row['cart_id'];
				$total = $row['area']*$row['plan_price'];
				$cart_info[$k]['income_expect'] = $row['income_expect'] * $row['area'];
				$cart_info[$k]['cart_price']  = $total;
				$cart_info[$k]['total_money'] = $total + $row['storage_price'];
				$result['total_money']       += $cart_info[$k]['total_money'];
			} else {
				unset($cart_info[$k]);
			}
		}

		// 获取用户可用资金
		$balance = M('UserAccount')->where(array('uid' => $this->uid))->getField('account_balance');
		$data['balance'] = $balance;
		$result['order_list']      = $cart_info;
		$result['total_num'] = count($cart_info);
		$data['cart_ids'] = $cart_ids;
		if ($result['total_num'] == 0) {
			$this->apiReturn(0,'购物车发生变化',$result);
		} else {
			$this->apiReturn(0,'成功',$result);
		}
	}

	/**
	 * 创建订单
	 */
	public function submit()
	{


		try {
			//开启事务
			$m = M('SellCart');
			$m->startTrans();

			//获取提交的数据
			$lists = array_unique((array)$this->data['cart_list']);
			if (empty($lists)) {
				throw new \Exception('提交的购物车为空',46404);
			}

            if (!isset($this->data['pay_pass']) || empty($this->data['pay_pass'])) {
                throw new \Exception('请输入您的支付密码!',45112);
            }
            //检测用户的支付密码是否正确
            $pay_info = $this->check_pay_pass($this->data['pay_pass']);
            if(!$pay_info['status']){
                throw new \Exception($pay_info['msg'],$pay_info['code']);
            }

			//查询购物车中的订单信息
			$map['cart.status'] = 1;
			$map['cart.user_id'] = $this->uid;
			$map['cart.cart_id'] = array('IN',$lists);

			//查询要创建订单的购物车信息
			$cart_info = $m->alias('cart')
				->field('cart.*,storage.storage_price,plan.discount_id,plan.plan_price,plan.seed_id,plan.income_expect,plan.area_surplus')
				->join('__PLAN_SELL__ plan ON plan.plan_id=cart.plan_id','LEFT')
				->join('__PLAN_STORAGE__ storage ON storage.storage_id=cart.storage_id','LEFT')
				->where($map)->select();
			if (!$cart_info) {
				throw new \Exception('提交的购物车不合法',46405);
			}

			//购物车统计
			$pay_total = 0; //购物车总价
			$pay_num = count($cart_info); //购物车数量
			foreach ($cart_info as $k=>$row) {
				//检验购物车状态
				$state = $this->stateCheck($row['plan_id'], $row['storage_id'], $row['area']);
				if ($state['status']) {
					$total = $row['area']*$row['plan_price'];
					$cart_info[$k]['income_all'] = $row['income_expect'] * $row['area'];
					$cart_info[$k]['order_price']  = $total;
					$cart_info[$k]['total_money'] = $total + $row['storage_price'];
					$pay_total += $cart_info[$k]['total_money'];
				} else {
					throw new \Exception('购物车信息发生变化',46406);
				}
			}

			//创建总单
			$pay['payment_sn'] = $this->makeSn($this->uid,'01');
			$pay['user_id'] = $this->uid;
			$pay['pay_total'] = $pay_total;
			$pay['pay_num'] = $pay_num;
			$pay['note'] = isset($this->data['note'])?$this->data['note']:'';
			$pay['add_time'] = $pay['update_time'] = NOW_TIME;

			//查询余额是否支持
			$user_account = M('UserAccount')->where(array('uid'=>$this->uid))->find();
			if ($user_account['account_balance'] < $pay['pay_total']) {

				throw new \Exception("账户余额不足",46502);
			}

			//确认支付
			$data['account_amount'] = $user_account['account_amount'] - $pay['pay_total'];
			$data['account_balance'] = $user_account['account_balance'] - $pay['pay_total'];
			$data['consume_amount'] = $user_account['consume_amount'] + $pay['pay_total'];
			$data['id'] = $user_account['id'];
			$account_res = M('UserAccount')->save($data);
			if (!$account_res) {
				throw new \Exception('支付失败',46503);
			}

			$account_log['uid'] = $user_account['uid'];
			$account_log['type'] = 'order_pay';
			$account_log['affect_money'] = $pay['pay_total'];
			$account_log['available_money'] = $data['account_balance'];
			$account_log['freeze_money'] = $user_account['freeze_amount'];
			$account_log['add_time'] = NOW_TIME;
			$account_log['add_ip'] = get_client_ip();
			$account_log['user_name'] = $this->user_name;
			$account_log['description'] = '购买种子 支付单号:' . $pay['payment_sn'];

			$log_res = M('UserAccountChange')->add($account_log);
			if (!$log_res) {
				throw new \Exception('添加支付日志失败',46504);
			}

			$res = M('SellOrderSummary')->add($pay);
			if (!$res) {
				throw new \Exception('系统繁忙，请稍候重试',-1);
			}

			//添加详单
			$all_order = array(); //要添加的详单
            $all_cart_id = array(); //购物车ID
			$time = NOW_TIME;
			foreach ($cart_info as $k => $v) {

                $all_cart_id[] = $v['cart_id'];

                $order = array();
				$order['payment_id'] = $res;
				$order['order_sn'] = $this->makeSn($this->uid,'02');
				$order['user_id'] = $this->uid;
				$order['plan_id'] = $v['plan_id'];
                $order['seed_id'] = $v['seed_id'];
				$order['storage_id'] = $v['storage_id'];
				$order['discount_id'] = $v['discount_id'];
				$order['order_area'] = $v['area'];
				$order['order_price'] = $v['order_price']; //总价
				$order['plan_price'] = $v['plan_price']; //单价
				$order['storage_price'] = $v['storage_price']; //存储费用
				$order['order_income'] = $v['income_all'];
				$order['plan_income'] = $v['income_expect'];
				$order['pay_total'] = $v['total_money']; //实际支付
				$order['add_time'] = $order['update_time'] = $time;
				$all_order[] = $order;

				//修改方案可购买剩余量
                $save_data = array();
                $save_data['plan_id'] = $v['plan_id'];
                $save_data['area_surplus'] = $v['area_surplus'] - $v['area'];
				$save_res = M('PlanSell')->save($save_data);
                if (false === $save_res) {
                    throw new \Exception('系统繁忙，请稍候重试',-1);
                }
			}

			$result = M('SellOrder')->addAll($all_order);
			if (!$result) {
				throw new \Exception('系统繁忙，请稍候重试',-1);
			}

			//订单操作日志 TODO

            $cart_info_map['cart_id'] = array('IN',$all_cart_id);
            $cart_info_data['status'] = -1;
            $cart_res = $m->where($cart_info_map)->save($cart_info_data);
            if ($cart_res) {
                $m->commit();
				$summary['payment_id'] = $res;
                $this->apiReturn(0,'成功',$summary);
            } else {
                throw new \Exception('清空购物车失败',46407);
            }
		} catch(\Exception $e) {
			$m->rollBack();
			$this->apiReturn($e->getCode() , $e->getMessage());
		}
	}

    /** todo 文档
     * 验证支付密码是否正确
     *
     * @param $pwd 密码
     * @return array 返回验证结构
     */
    private function check_pay_pass($pwd){

        $model = D('User');
        $user_info = $model->field('pay_pass,pay_encrypt')->where(array('uid'=>$this->uid))->find();
        if(false === $user_info){
            return array('status'=>false,'msg'=>'数据查询失败，请稍后！','code'=>45120);
        }
        if(empty($user_info['pay_pass'])){
            return array('status'=>false,'msg'=>'尊敬的用户，您还未设置支付密码，请设置！','code'=>45121);
        }
        //TODO 加密验证
        if($model->hashPassword($pwd,$user_info['pay_encrypt']) == $user_info['pay_pass']){
            $value = array('status'=>true,'code'=>0);

        }else{
            $value = array('status'=>false,'msg'=>'请输入正确的支付密码！','code'=>45122);
        }
        return $value;

    }
	/**
	 * 统计购物车
	 * @param bool $return 是否输出
	 */
	public function cartcount($return = false)
	{
		//查询条件
		$cart = M('SellCart');
		$map['cart.status']  = 1;
		$map['cart.user_id'] = $this->uid;
		//查询购物车列表
		$res = $cart->alias('cart')
			->join('__PLAN_SELL__ plan ON plan.plan_id=cart.plan_id','LEFT')
			->join('__PLAN_STORAGE__ storage ON storage.storage_id=cart.storage_id','LEFT')
			->field('storage.storage_price,storage.storage_id,cart.area,plan.plan_price,plan.plan_id')
			->where($map)->select();
		//购物车信息
		$result['total_num']   = 0;
		$result['total_money'] = 0;
		if ($res) {
			foreach ($res as $k => $row) {
				$state = $this->countCheck($row['plan_id'], $row['storage_id'], $row['area']);
				if ($state) {
					$result['total_money'] += $row['area'] * $row['plan_price'] + $row['storage_price'];
				}
			}
			$result['total_num'] = count($res);
		}
		//删除无效购物车
		/*if (isset($destroy)) {
            $maps['cart_id'] = array('IN',$destroy);
            $data['status'] = -1;
            $cart->where($maps)->save($data);
        }*/
		if ($return) {
			return $result;
		} else {
			$this->apiReturn(0, '成功', $result);
		}
	}

	/**
	 * 统计验证
	 * @param  int $plan_id 方案ID
	 * @param  int $storage_id 购买方案ID
	 * @param  float $area 购买面积限制
	 * @return mixed
	 */
	private function countCheck($plan_id, $storage_id, $area)
	{
		//验证销售方案
		$map['status']  = 1;
		$map['plan_id'] = $plan_id;
		$res = M('PlanSell')->where($map)->find();
		if (!$res) {
			return false;
		}

		//验证存储方式
		$storage = explode(',',$res['storage_id']);
		if (!in_array($storage_id, $storage)) {
			return false;
		}

		//定义静态变量，存储每个方案购买面积的累加
		static $total = array();
		if (isset($total[$plan_id])) {
			$total[$plan_id] += $area;
		} else {
			$total[$plan_id] = $area;
		}

		//检查购买面积
		if ($total[$plan_id]<0) {
			return false;
		}

		$surplus = $res['area_surplus'] - $total[$plan_id];
		if ($surplus < $res['area_min']) {
			if ($surplus != 0) {
				return false;
			}
		} else {
			if ($total[$plan_id] < $res['area_min']) {
				return false;
			}

			//比较可购买面积
			if ($res['area_max'] != 0) {
				if ($total[$plan_id] > $res['area_max']) {
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * 购买面积及方案验证
	 *
	 * @param  int $plan_id 方案ID
	 * @param  int $storage_id 购买方案ID
	 * @param  float $area 购买面积限制
	 * @return mixed
	 */
	private function stateCheck($plan_id, $storage_id, $area)
	{
		//返回内容
		$result['status'] = false;
		$result['data']   = $area;
		$result['msg']    = '正常';

		//验证销售方案
		$map['status']  = 1;
		$map['plan_id'] = $plan_id;
		$res = M('PlanSell')->where($map)->find();
		if (!$res) {
			$result['msg'] = '无效的销售方案';
			return $result;
		}

		//验证存储方式
		$storage = explode(',',$res['storage_id']);
		if (!in_array($storage_id, $storage)) {
			$result['msg'] = '无效的存储方式';
			return $result;
		}

		//定义静态变量，存储每个方案购买面积的累加
		static $total = array();
        if (isset($total[$plan_id])) {
            $total[$plan_id] += $area;
        } else {
            $total[$plan_id] = $area;
        }

		//检查购买面积
		if ($total[$plan_id]<0) {
			$result['msg'] = '够买面积不能是负数';
			return $result;
		}

		//剩余面积
		//总购买面积		= 已经验证的面积 + 本次购买面积
		//购买后剩余面积 = 可购买面积 - 总购买面积
		$surplus = $res['area_surplus'] - $total[$plan_id];
		if ($surplus < $res['area_min']) {
			if ($surplus == 0) {
				$result['status'] = true;
				$result['data'] = $area;
			} else if ($surplus > 0){
				$result['msg'] = '方案限制只能购买'. ($surplus + $area) . ' 平米';
				$result['data'] = ($surplus + $area);
			} else {
				$result['msg'] = '方案剩余'. ($res['area_surplus'] - $total[$plan_id] + $area) . ' 平米';
				$result['data'] = $res['area_surplus'] - $total[$plan_id] + $area;
				if ($res['area_max'] && $result['data'] > $res['area_max']) {
					$result['msg'] = '方案限制最多购买 ' . $res['area_max'] . ' 平米';
					$result['data'] = $res['area_max'];
				}
			}
			return $result;
		} else {
			if ($total[$plan_id] < $res['area_min']) {
				$result['msg'] = '方案限制最少购买 ' . $res['area_min'] . ' 平米';
				$result['data'] = $res['area_min'];
				return $result;
			}

			//比较可购买面积
			if ($res['area_max'] != 0) {
				if ($total[$plan_id] > $res['area_max']) {
					$result['msg'] = '方案限制最多购买 ' . $res['area_max'] . ' 平米';
					$result['data'] = $res['area_max'];
					return $result;
				}
			}

			$result['status'] = true;
			return $result;

		}
	}

	/**
	 * 订单编号生成规则，n(n>=1)个订单表对应一个支付表，
	 * 生成订单编号(年取1位 + $uid取13位 + 第N个子订单取2位)
	 * 1000个会员同一微秒提订单，重复机率为1/100
	 * @param int $type 订单类型 总单01 订单02
	 * @param int $uid 用户ID
	 * @return string
	 */
	private function makeSn($uid,$type)
	{
		//记录生成子订单的个数，如果生成多个子订单，该值会累加
		static $num;
		if (empty($num)) {
			$num = 1;
		} else {
			$num++;
		}
		return $type . sprintf('%010d', time() - 946656000) . sprintf('%03d', (float)microtime() * 1000) . sprintf('%03d', (int)$uid % 1000) . sprintf('%02d', $num);
	}
}