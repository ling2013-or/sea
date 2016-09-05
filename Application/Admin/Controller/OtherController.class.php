<?php
namespace Admin\Controller;

use Think\Controller;

/**
 * 农场物流管理控制器
 * Class FarmController
 * @package Admin\Controller
 */
class OtherController extends AdminController
{
    /**
     * 快递公司列表
     */
    public function delivery()
    {
        //获取快递公司列表
        $where = array();
        //搜索
        if (isset($_GET['query'])) {
            $query = I('query');
            $map['id'] = array('eq', $query);
            $map['name'] = array('eq', $query);
            $map['_logic'] = 'or';
            $where['_complex'] = $map;
        }
        //分页
        $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        //统计总数
        $total = M('Express')->where($where)->count();
        //实例化分页类
        $page = new \Think\Page($total, $listRows);
        $p = $page->show();
        //每页显示数量个数
        $limit = $page->firstRow . ',' . $page->listRows;
        //获取所有的服务地址
        $lists = M('Express')->field('*')
            ->where($where)
            ->order('id ASC')
            ->limit($limit)
            ->select();
        $this->assign('page', $p ? $p : '');
        $this->assign('total', $total);
        $this->assign('lists', $lists);
        $this->meta_title = '公司列表';
        $this->display();
    }

    /**
     * 添加农场发货地址
     */
    public function deliveryAdd()
    {
        if (IS_POST) {
            //实例化model
            $message = D('Express');
            //验证数据是否正常
            if ($message->create()) {
                //TODO 组装地址 address
                //将数据插入数据库
                $message->add();
                $this->success('发货地址添加成功！', U('delivery'));
            } else {
                $this->error($message->getError());
            }

        }
        $this->meta_title = '添加物流公司';
        $this->display();
    }

    /**
     * 编辑短信模板信息
     * @param string $id 社区唯一ID
     */
    public function deliveryEdit($id = '')
    {
        if (IS_POST) {

            //实例化一个model
            $message = D('Express');
            $data = $message->create();
            //判断值得格式
            if ($data) {
                //TODO 组装地址 address
                //更新数据库
                $message->save();
                $this->success('修改成功', U('delivery'));
            } else {
                $this->error($message->getError());
            }
        }

        //通过ID获取物流公司信息
        $article = D('Express');
        $result = $article->lists();
        //设置标题
        $this->meta_title = '编辑公司信息';
        $this->assign('list', $result['0']);

        $this->display();
    }

    /**
     * 敏感词汇列表
     */
    public function sensitive()
    {
        //获取快递公司列表
        $where = array();
        //搜索
        if (isset($_GET['query']) && !empty($_GET['query'])) {
            $query = I('query');
            $map['id'] = array('eq', $query);
            $map['vocabulary'] = array('eq', $query);
            $map['_logic'] = 'or';
            $where['_complex'] = $map;
        }
        //分页
        $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        //统计总数
        $total = M('SensitiveVocabulary')->where($where)->count();
        //实例化分页类
        $page = new \Think\Page($total, $listRows);
        $p = $page->show();
        //每页显示数量个数
        $limit = $page->firstRow . ',' . $page->listRows;
        //获取所有的服务地址
        $lists = M('SensitiveVocabulary')->field('*')
            ->where($where)
            ->order('id ASC')
            ->limit($limit)
            ->select();

        $this->assign('page', $p ? $p : '');
        $this->assign('total', $total);
        $this->assign('lists', $lists);
        $this->meta_title = '公司列表';
        $this->display();
    }

    /**
     * 添加农场发货地址
     */
    public function sensitiveAdd()
    {
        if (IS_POST) {
            //实例化model
            $message = D('SensitiveVocabulary');
            //验证数据是否正常
            if ($message->create()) {

                //将数据插入数据库
                $message->add();
                $this->success('词汇添加成功！', U('sensitive'));
            } else {
                $this->error($message->getError());
            }

        }
        $this->meta_title = '添加敏感词汇';
        $this->display();
    }

    /**
     * 编辑短信模板信息
     * @param string $id 社区唯一ID
     */
    public function sensitiveEdit($id = '')
    {
        if (IS_POST) {

            //实例化一个model
            $message = D('SensitiveVocabulary');
            $data = $message->create();
            //判断值得格式
            if ($data) {
                //TODO 组装地址 address
                //更新数据库
                $message->save();
                $this->success('修改成功', U('sensitive'));
            } else {
                $this->error($message->getError());
            }


        }

        //通过ID获取物流公司信息
        $article = D('SensitiveVocabulary');
        $result = $article->lists();
        //设置标题
        $this->meta_title = '编辑公司信息';
        $this->assign('list', $result['0']);

        $this->display();
    }

}