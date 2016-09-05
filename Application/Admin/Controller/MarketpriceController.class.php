<?php
namespace Admin\Controller;

/**
 * 商品价格管理
 * Class MarketpriceController
 * @package Admin\Controller
 */
class MarketpriceController extends AdminController
{

    /**
     * 历史价格列表
     */
    public function index()
    {
        $map = array();
        if (isset($_GET['name']) && $_GET['name'] !== '') {
            $map['seed.seed_name'] = array('LIKE','%' . $_GET['name'] . '%');
        }

        //分页
        $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        $total = M('MarketPrice')->alias('mp')
            ->join('__SEED__ seed ON seed.seed_id=mp.seed_id','LEFT')
            ->where($map)->count();
        $page = new \Think\Page($total, $listRows);
        $p = $page->show();
        $limit = $page->firstRow . ',' . $page->listRows;
        
        //数据列表
        $lists = M('MarketPrice')->alias('mp')
            ->join('__SEED__ seed ON seed.seed_id=mp.seed_id','LEFT')
            ->field('mp.*,seed.seed_name')->where($map)->limit($limit)->order('mp.add_time DESC')->select();

        // 记录当前列表页的cookie
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->meta_title = '商城种子价格列表';
        
        $this->assign('page', $p ? $p : '');
        $this->assign('total', $total);
        $this->assign('lists', $lists);
        $this->display();
    }

    /**
     * 添加折扣方案
     */
    public function add()
    {
        if (IS_POST) {
            $Plan = M('MarketPrice');
            $ipt = $_POST;
            if (empty($_POST)) {
                $this->error('未提交任何数据');
            }
            foreach ($ipt['seed_id'] as $k=>$v) {
                if ($v == '') continue;
                if ($ipt['seed_price'][$k] == '') continue;
                $tmp['seed_id'] = $v;
                $tmp['price'] = $ipt['seed_price'][$k];
                $tmp['add_time'] = $tmp['day_time'] = NOW_TIME;
                $data[] = $tmp;
            }
            if (!empty($data)) {
                if ($Plan->addAll($data)) {
                    $this->success('新增成功', U('index'));
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error('未提交任何有效数据');
            }
        } else {
            $this->meta_title = '添加当日价格';
            //查询所有种子
            $seeds = M('Seed')->where("status=1")->select();
            $this->assign('seeds',$seeds);
            $this->display();
        }  
    }
}