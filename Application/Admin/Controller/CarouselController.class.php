<?php
namespace Admin\Controller;

/**
 * 首页轮播图管理
 * Class SellplanController
 * @package Admin\Controller
 */
class CarouselController extends AdminController
{

    /**
     * 轮播图列表
     *
     * 状态为0[未发布] 和 1[已发布]
     * 只有已发布时才能设置收益
     */
    public function index()
    {
        $map['status'] = array('neq', -1);
        if (isset($_GET['name']) && $_GET['name'] !== '') {
            $map['title'] = array('like', '%' . (string)I('name') . '%');
        }

        //分页
        $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        $total = M('Carousel')->where($map)->count();
        $page = new \Think\Page($total, $listRows);
        $p = $page->show();
        $limit = $page->firstRow . ',' . $page->listRows;

        //数据列表
        $lists = M('Carousel')->field('*')->where($map)->limit($limit)->order('status,title')->select();

        // 记录当前列表页的cookie
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->meta_title = '养殖方案列表';

        $this->assign('page', $p ? $p : '');
        $this->assign('today', NOW_TIME);
        $this->assign('lists', $lists);
        $this->display();
    }

    /**
     * 添加方案
     */
    public function add()
    {
        if (IS_POST) {
            $Plan = D('Carousel');
            $data = $Plan->create();
            if (empty($data['title'])) $this->error('请填写标题');
            if (empty($data['model'])) $this->error('请填写模块标识');

            foreach ($data as $k => $v) {
                if ($v == '') {
                    unset($data[$k]);
                }
            }

            //获取图片数量
            if (isset($data['img'])) {

                $data['num'] = count(json_decode($data['img']));
            }
            $data['add_time'] = NOW_TIME;

            //创建计划任务
            if ($Plan->add($data)) {
                $this->success('新增成功', U('index'));
            } else {
                $this->error('新增失败');
            }
        } else {
            //查询可添加的产品养殖计划
            $this->meta_title = '新增养殖计划';
            $this->display();
        }
    }

    /**
     * 编辑方案
     * @param   int $id 待修改的ID
     */
    public function edit($id = 0)
    {
        if (IS_POST) {
            $Plan = D('Carousel');
            $data = $Plan->create();
            if (empty($data['title'])) $this->error('请填写标题');
            if (empty($data['model'])) $this->error('请填写模块标识');

            foreach ($data as $k => $v) {
                if ($v == '') {
                    unset($data[$k]);
                }
            }

            //获取图片数量
            if (isset($data['img'])) {
                $data['num'] = count(json_decode($data['img'], true));
            }
            $data['update_time'] = NOW_TIME;
            if ($Plan->save($data)) {
                $this->success('更新成功', Cookie('__forward__'));
            } else {
                $this->error('更新失败');
            }
        } else {
            //获取数据
            $info = M('Carousel')->field(true)->find($id);

            $map['status'] = array('neq', -1);
            $info['img'] = json_decode($info['img'], true);
            $info['url'] = json_decode($info['url'], true);

            $this->meta_title = '编辑轮播图';
            $this->assign('info', $info);
            $this->display();
        }
    }

    /**
     * 删除方案
     *
     * 仅可以删除未交易的方案
     */
    public function del()
    {
        $id = intval(I('id'));
        $data['id'] = $id;
        $data['status'] = -1;
        $m = M('Carousel');
        $info = $m->find($id);
        if (!$info) {
            $this->error('未找到该图片');
        }

        //删除图片
        if ($m->save($data)) {
            $this->success('删除成功');
        }

        $this->error('删除失败！');
    }

    /**
     * 获取农场分区
     */
    public function getblock()
    {
        if (IS_POST) {
            $farm_id = I('id');
            if ($farm_id) {
                $map['status'] = 1;
                $map['farm_id'] = $farm_id;
                $map['area_used'] = array('gt', 0);
                $blockList = M('FarmBlock')->field(true)->where($map)->select();
                if (empty($blockList)) {
                    $this->error('未找到数据,无法选择');
                } else {
                    $cameraMap['status'] = 1;
                    $cameraMap['farm_id'] = $farm_id;
                    $cameraList = M('Camera')->field('id,title')->where($cameraMap)->select();
                    $list['block'] = $blockList;
                    $list['camera'] = $cameraList;
                    $this->ajaxReturn($list);
                }
            } else {
                $this->error('找不到分区');
            }
        } else {
            $this->display('add');
        }
    }

    /**
     * 编辑状态
     */
    public function dopush()
    {
        $id = intval(I('id'));
        $status = intval(I('status'));
        if (empty($id)) {
            $this->error('请选择要操作的数据!');
        }

        //更新方案状态为1
        $data['id'] = $id;
        $data['status'] = $status;
        if (M('Carousel')->save($data)) {
            $this->success('修改成功');
        } else {
            $this->error('修改失败！');
        }
    }

    /**
     * 设置方案收益
     */
    public function docomplete()
    {
        $id = intval(I('id'));
        $income = floatval(I('income'));
        if (empty($id)) {
            $this->error('请选择要操作的数据!');
        }

        //更新income_real
        $data['plan_id'] = $id;
        $data['income_real'] = $income;
        if (M('PlanSell')->save($data)) {
            $this->success('设置成功');
        } else {
            $this->error('设置失败！');
        }
    }

    /**
     * 方案编号生成
     * 0~2   01 种子方案 02 存储方案 03 折扣方案 04 销售方案
     * 3~8   年月日
     * 9~10  两位随机码
     * 11~15 时分秒的字符串
     */
    private function snmake()
    {
        static $index = 1;
        $static = sprintf("%02d", $index);
        $index++;
        $his = sprintf("%005d", time() - strtotime(date('Y-m-d')));
        $rand = sprintf("%002d", mt_rand(0, 99));
        return '04' . date('ymd') . $rand . $his . $static;
    }
}