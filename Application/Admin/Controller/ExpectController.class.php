<?php
namespace Admin\Controller;

/**
 * 用户产量管理
 * Class ExpectController
 * @package Admin\Controller
 */
class ExpectController extends AdminController
{
    /**
     * 预期产量列表
     */
    public function index()
    {
        $map = array();
        // TODO 搜索条件

        $username = I('username', '', 'trim');
        if(!empty($username)) {
            $map['user.user_name'] = array('like', '%' . (string)I('username') . '%');
        }

        $total = M('UserExpect')
            ->alias('expect')
            ->join('__USER__ AS user ON user.uid = expect.uid', 'LEFT')
            ->join('__PLAN_SELL__ AS plan ON expect.plan_id = plan.plan_id', 'LEFT')
            ->join('__FARM__ as farm ON plan.farm_id = farm.farm_id', 'LEFT')
            ->join('__SEED__ AS seed ON seed.seed_id = plan.seed_id', 'LEFT')
            ->where($map)
            ->count();
        $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        $page = new \Think\Page($total, $listRows);
        $p = $page->show();
        $limit = $page->firstRow . ',' . $page->listRows;
        $lists = M('UserExpect')
            ->alias('expect')
            ->join('__USER__ AS user ON user.uid = expect.uid', 'LEFT')
            ->join('__PLAN_SELL__ AS plan ON expect.plan_id = plan.plan_id', 'LEFT')
            ->join('__FARM__ as farm ON plan.farm_id = farm.farm_id', 'LEFT')
            ->join('__SEED__ AS seed ON seed.seed_id = plan.seed_id', 'LEFT')
            ->field('expect.status,expect.id,expect.plant_area,expect.expect_yield,expect.real_yield,user.user_name,plan.plan_name,plan.plan_sn,plan.plan_start,plan.plan_end,farm.farm_name,seed.seed_name')
            ->where($map)
            ->limit($limit)
            ->order('id desc')
            ->select();
        $this->meta_title = '预期产量管理';
        // 记录当前列表页的cookie
        Cookie('__forward__', $_SERVER['REQUEST_URI']);

        $this->assign('page', $p ? $p : '');
        $this->assign('total', $total);
        $this->assign('lists', $lists);
        $this->assign('today', NOW_TIME);
        $this->display();
    }

    /**
     * 添加实际产量
     *
     * 用户收益
     */
    public function doComplete()
    {
        if (IS_POST) {
            $m = M('UserExpect');
            $m->startTrans();
            $id = I('id');
            $real = (float)I('real',0);

            try {
                //添加收益数据，修改收益状态
                $map['id'] = $id;
                $map['status'] = 0;
                $data['real_yield'] = $real;
                $data['status'] = 1;
                $res = $m->where($map)->save($data);
                if (!$res) throw new \Exception('收益更新失败');

                //添加库存
                $maps['u.id'] = $id;
                $info = $m->alias('u')
                ->join('__PLAN_SELL__ AS p ON u.plan_id=p.plan_id','left')
                ->field('u.*,p.plan_start,p.plan_end')
                ->where($maps)->find();
                $arr['plan_id'] = $info['plan_id'];
                $arr['user_id'] = $info['uid'];
                $arr['initialize'] = $arr['stock'] = $info['real_yield'];
                $arr['start_time'] = $info['plan_start'];
                $arr['end_time'] = $info['plan_end'];
                $arr['add_time'] = $arr['update_time'] = NOW_TIME;
                $sid = M('SellStorage')->add($arr);
                if (!$sid) throw new \Exception("添加库存失败");
                
                //添加库存日志
                $log['descript'] = '收益';
                $log['user'] = $info['uid'];
                $log['storage_id'] = $sid;
                $log['storage_stock'] = $log['storage_change'] = $info['real_yield'];
                $log['operate_time'] = NOW_TIME;
                $res = M('StorageLog')->add($log);
                if ($res) {
                    $m->commit();
                    $this->success('操作成功'); 
                } else {
                    throw new \Exception("添加日志失败");   
                }
            } catch(\Exception $e) {
                $m->rollback();
                $this->error($e->getMessage());
            }
        } else {
            $this->error('操作错误');
        }
    }
}