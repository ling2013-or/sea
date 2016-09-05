<?php
namespace Admin\Controller;

/**
 * 用户库存管理
 * @package Admin\Controller
 */
class StorageController extends AdminController
{

    /**
     * 总库存列表
     */
    public function index()
    {
        //搜索条件
        $m = M('UserStorageSummary');
        $map = array();

        //查询条件
        if (isset($_GET['kw']) && isset($_GET['vw']) && $_GET['vw'] !== '') {
            switch (I('kw')) {
                case 1:
                    //查询作物名称对应的ID
                    $map['storage.seed_name'] = array('like','%'. I('vw') .'%');
                    break;
                case 2:
                    //查询用户名称对应的ID
                    $map['user.user_name'] = array('like','%'. I('vw') .'%');
                    break;
                default:
                    break;
            }
        }

        //分页
        $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        $total = $m->alias('storage')
            ->join('__USER__ user ON storage.user_id=user.uid', 'LEFT')
            ->where($map)->count();
        $page = new \Think\Page($total, $listRows);
        $p = $page->show();
        $limit = $page->firstRow . ',' . $page->listRows;

        //数据列表
        $lists = $m->alias('storage')
            ->field('storage.*, user.user_name, user.is_platform')
            ->join('__USER__ user ON storage.user_id=user.uid', 'LEFT')
            ->where($map)->limit($limit)->select();

        // 记录当前列表页的cookie
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->meta_title = '库存管理';

        $this->assign('page', $p ? $p : '');
        $this->assign('total', $total);
        $this->assign('lists', $lists);
        $this->display();

    }

    /**
     * 库存详情列表
     */
    public function details()
    {
        $map = array();

        $id = I('summary_id', 0, 'intval');
        if ($id) {
            $map['storage.summary_id'] = $id;
        }

        //查询条件
        if (isset($_GET['kw']) && isset($_GET['vw']) && $_GET['vw'] !== '') {
            switch (I('kw')) {
                case 1:
                    //查询方案名称对应的ID
                    $map['plan.plan_name'] = array('like','%'. I('vw') .'%');
                    break;
                case 2:
                    //查询作物名称对应的ID
                    $map['storage.seed_name'] = array('like','%'. I('vw') .'%');
                    break;
                case 3:
                    //查询用户名称对应的ID
                    $map['user.user_name'] = array('like','%'. I('vw') .'%');
                    break;
                default:
                    break;
            }
        }

        //分页
        $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        $total = M('UserStorage')->alias('storage')
            ->join('__USER__ user ON storage.user_id=user.uid','LEFT')
            ->join('__PLAN_SELL__ plan ON storage.plan_id=plan.plan_id','LEFT')
            ->where($map)->count();
        $page = new \Think\Page($total, $listRows);
        $p = $page->show();
        $limit = $page->firstRow . ',' . $page->listRows;
        
        //数据列表
        $lists = M('UserStorage')->alias('storage')
            ->field('storage.*,user.user_name,plan.plan_name,user.is_platform')
            ->join('__USER__ user ON storage.user_id=user.uid','LEFT')
            ->join('__PLAN_SELL__ plan ON storage.plan_id=plan.plan_id','LEFT')
            ->where($map)->limit($limit)->select();

        // 记录当前列表页的cookie
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->meta_title = '库存管理';
        
        $this->assign('page', $p ? $p : '');
        $this->assign('total', $total);
        $this->assign('lists', $lists);
        $this->display();
    }

    /**
     * 库存日志
     */
    public function log()
    {
        $user_id = I('user_id', 0, 'intval');
        $plan_id = I('plan_id', 0, 'intval');
        if (empty($user_id) || empty($user_id)) {
            $this->error('无法获取库存记录！');
        } else {
            //查询条件
            /* 时间段查询 */
            if (isset($_GET['start_time'])) {
                $start_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/', $_GET['start_time']);
                $start_unixtime = $start_time ? strtotime($_GET['start_time']) : null;
                if ($start_unixtime) {
                    $map['logs.operate_time'][] = array('EGT', $start_unixtime);
                }
            }

            if (isset($_GET['end_time'])) {
                $end_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/', $_GET['end_time']);
                $end_unixtime = $end_time ? strtotime($_GET['end_time']) : null;
                if ($end_unixtime) {
                    $map['logs.operate_time'][] = array('LT', $end_unixtime + 86400);
                }
            }


            $map['plan_id'] = $plan_id;
            $map['user_id'] = $user_id;

            //分页
            $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
            $total = M('UserStorageLog')->alias('logs')->where($map)->count();
            $page = new \Think\Page($total, $listRows);
            $p = $page->show();
            $limit = $page->firstRow . ',' . $page->listRows;

            $lists = M('UserStorageLog')->alias('logs')
                ->join('__USER__ user ON user.uid=logs.user_id','LEFT')
                ->field('logs.*,user.user_name')->where($map)->limit($limit)->order('operate_time')->select();

            // 记录当前列表页的cookie
            Cookie('__forward__', $_SERVER['REQUEST_URI']);
            $this->meta_title = '库存记录';

            $this->assign('page', $p ? $p : '');
            $this->assign('total', $total);
            $this->assign('lists', $lists);
            $this->display();
        }
    }
}