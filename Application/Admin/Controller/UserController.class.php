<?php
namespace Admin\Controller;

/**
 * 会员管理
 * Class UserController
 * @package Admin\Controller
 */
class UserController extends AdminController
{
    /**
     * 会员管理列表
     */
    public function index()
    {
        $where = array();
        if($_GET['query']){
            $map['user.user_name'] = I('query');
            $map['user.user_phone'] = I('query');
            $map['_logic'] = 'or';
            $where['_complex'] = $map;
        }
        /* 时间段查询 */
        if(isset($_GET['start_time'])) {
            $start_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/', $_GET['start_time']);
            $start_unixtime = $start_time ? strtotime($_GET['start_time']) : null;
            if($start_unixtime) {
                $where['user.reg_time'][] = array('EGT', $start_unixtime);
            }
        }

        if(isset($_GET['end_time'])) {
            $end_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/', $_GET['end_time']);
            $end_unixtime = $end_time ? strtotime($_GET['end_time']) : null;
            if ($end_unixtime) {
                $where['user.reg_time'][] = array('LT', $end_unixtime + 86400);
            }
        }
        $where['user.status'] = array('neq', -1);
        // 只统计主表信息，账户金额不作为搜索条件
        $total = M('User')->alias('user')->where($where)->count();
        $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        $page = new \Think\Page($total, $listRows);
        $p = $page->show();
        $limit = $page->firstRow . ',' . $page->listRows;

        $field = 'user.*,level.level_name';
        $lists = M('User')->alias('user')
            ->join('__USER_LEVEL__ AS level ON user.lid = level.level_id', 'LEFT')
            ->field($field)
            ->where($where)
            ->limit($limit)
            ->order('uid DESC')
            ->select();

        $this->meta_title = '会员管理';
        // 记录当前列表页的cookie
        Cookie('__forward__', $_SERVER['REQUEST_URI']);

        $this->assign('page', $p ? $p : '');
        $this->assign('total', $total);
        $this->assign('lists', $lists);
        $this->display();
    }

    /**
     * 添加会员账户
     * 注：添加会员的时候同时将会员的资金账户添加上
     */
    public function add()
    {
        if(IS_POST) {
            $Model = D('User');
            $res = $Model->addUser();
            if($res) {
                $this->success('添加会员成功', Cookie('__forward__'));
            } else {
                $this->error($Model->getError());
            }
        } else {
            $this->meta_title = '添加会员';
            $this->display();
        }
    }

    /**
     * 编辑用户
     */
    public function edit()
    {
        $uid = I('uid', 0, 'intval');
        if(empty($uid)) {
            $this->error('请选择要编辑的用户');
        }
        $info = M('User')->where(array('uid'=>$uid, 'status'=>array('neq', -1)))->find();
        if(!$info) {
            $this->error('该用户不存在');
        }

        if(IS_POST) {
            $Model = D('User');
            if($Model->create()) {
                $res = $Model->where(array('uid'=>$uid))->save();
                if(false !== $res) {
                    $this->success('编辑用户信息成功', Cookie('__forward__'));
                } else {
                    $this->error('编辑用户信息失败');
                }
            } else {
                $this->error($Model->getError());
            }
        } else {
            $this->meta_title = '编辑会员';
            $this->assign('info', $info);
            $this->display();
        }
    }

    /**
     * 更改用户状态
     */
    public function state()
    {
        // 获取用户ID
        $uid = I('uid', 0, 'intval');
        if(empty($uid)) {
            $this->error('请选择要处理的用户');
        }

        $state = I('status', 0, 'intval');
        if(!in_array($state, array(0, 1, 2))) {
            $this->error('用户状态不存在');
        }

        $user = M('User')->field(true)->where(array('uid'=>$uid, 'status'=>array('neq', -1)))->find();
        if(!$user) {
            $this->error('用户不存在');
        }

        $res = M('User')->where(array('uid'=>$uid))->save(array('status'=>$state));
        if(false === $res) {
            $this->error('更改用户状态失败');
        } else {
            $this->success('更改用户状态成功');
        }
    }

    /**
     * 等级列表
     */
    public function level(){
        $where = array();
        if($_GET['query']){
            $map['level_name'] = I('query');
            $map['level_id'] = I('query');
            $map['_logic'] = 'or';
            $where['_complex'] = $map;
        }
        // 只统计主表信息，账户金额不作为搜索条件
        $total = M('userLevel')->alias('t1')->where($where)->count();
        $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        $page = new \Think\Page($total, $listRows);
        $p = $page->show();
        $limit = $page->firstRow . ',' . $page->listRows;

        $field = 't1.*,t2.true_name';
        $lists = M('userLevel')->alias('t1')
            ->join('__ADMIN__ AS t2 ON t1.uid = t2.admin_id', 'LEFT')
            ->field($field)
            ->where($where)
            ->limit($limit)
            ->order('level_id')
            ->select();

        $this->meta_title = '会员管理';

        $this->assign('page', $p ? $p : '');
        $this->assign('total', $total);
        $this->assign('lists', $lists);
        $this->display();
    }


    /**
     * 添加会员账户等级
     * 注：添加会员的时候同时将会员的资金账户添加上
     */
    public function addLevel()
    {
        if(IS_POST) {
//            dump($_POST);die;
            $level = D('UserLevel');
            if($level->create()){
                $res = $level->add();
                if($res) {
                    $this->success('添加成功', U('level'));
                } else {
                    $this->error('toamjkoasdfsdf');
                }
            } else {
                $this->error($level->getError());
            }

        }
            $this->meta_title = '添加等级';
            $this->display();

    }

    /**
     * 编辑汇演等级信息
     */
    public function editLevel()
    {
        if(IS_POST){

            //实例化一个model
            $message = D('UserLevel');
            //判断值得格式
            if($message->create()){
                //更新数据哭
                $message->save();
                $this->success('修改成功',U('level'));
            }else{
                $this->error($message->getError());
            }


        }

        //通过article获取文章信息
        $article = D('UserLevel');
        $result = $article->lists();
        //设置标题
        $this->meta_title = '编辑等级';
        $this->assign('list',$result['0']);

        $this->display();
    }


    /**
     * 资金变动列表
     */
    public function fundChange()
    {
        $where = array();
        if($_GET['query']){
            $map['id'] = I('query');
            $map['user_name'] = I('query');
            $map['_logic'] = 'or';
            $where['_complex'] = $map;
        }
        /* 时间段查询 */
        if(isset($_GET['start_time'])) {
            $start_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/', $_GET['start_time']);
            $start_unixtime = $start_time ? strtotime($_GET['start_time']) : null;
            if($start_unixtime) {
                $where['add_time'][] = array('EGT', $start_unixtime);
            }
        }

        if(isset($_GET['end_time'])) {
            $end_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/', $_GET['end_time']);
            $end_unixtime = $end_time ? strtotime($_GET['end_time']) : null;
            if ($end_unixtime) {
                $where['add_time'][] = array('LT', $end_unixtime + 86400);
            }
        }

        /*状态查询*/
        if($_GET['type']){
            $where['type'] = array('eq',I('type'));
        }

        // 只统计主表信息，账户金额不作为搜索条件
        $total = M('UserAccountChange')->where($where)->count();
        $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        $page = new \Think\Page($total, $listRows);
        $p = $page->show();
        $limit = $page->firstRow . ',' . $page->listRows;

        $field = '*';
        $lists = M('UserAccountChange')
            ->field($field)
            ->where($where)
            ->limit($limit)
            ->order('id DESC')
            ->select();
        $this->meta_title = '资金变动';
        $this->assign('page', $p ? $p : '');
        $this->assign('total', $total);
        $this->assign('lists', $lists);
        $this->display();
    }


    /**
     * 查看收货地址
     */
    public function showaddr()
    {
        $uid = I("uid");
        if($uid<=0 or ( !is_numeric($uid) ))
        {
            $this->error("非法操作，参数不正确！");
        }

        $where = array();
        $where['addr.uid'] = $uid;
        $lists = M("user_address")->alias("addr")
//            ->join('__AREA__ AS province ON addr.provice_id = province.area_id', 'LEFT')
            ->join('__AREA__ AS city ON addr.city_id = city.area_id', 'LEFT')
            ->join('__AREA__ AS area ON addr.province_id = area.area_id', 'LEFT')
            ->field("addr.*,city.area_name as cname,area.area_name as pname") //province.area_name as pname,
            ->where($where)
            ->order("addr.is_default desc")
            ->select();
        $this->assign('lists', $lists);
        $this->display();
    }

    /**
     * 批量添加 -平-台- 用户
     */
    public function addbatch()
    {
        if (IS_POST) 
        {

            //添加数据
            if ($_POST['num'] and is_numeric($_POST['num']) and ($_POST['num']>=1)) 
            {
                $number = intval($_POST['num']);
                $Model = D('User');
                $prex = $_POST['prex']?$_POST['prex']:"farm_"; // 用户名前缀
                if(substr($prex, -1)!='_')
                {
                    $prex = $prex.'_';
                }

                $str = 'abcdefghkmnpqrstuvwxyz3456789'; 
                

                $tem_user_names = array();// 每添加一个就往里面写一个，以检查用户名是否重复
                for($i=1;$i<=$number;$i++)
                {
                    $data = array();
                    
                    $name = ''; 
                    $len = strlen($str)-1;
                    for($j = 0;$j < 6;$j ++) // 字符串长度为6
                    { 
                        $num1 = mt_rand(0, $len); 
                        $name .= $str[$num1]; 
                    }

                    while ( in_array($name, $tem_user_names) or M("user")->where("user_name='".$prex.$name."'")->find() ) // 如果已经存在，则重新生成
                    {//echo '有相同串：'.$name;die;
                        $name = ''; 
                        $len = strlen($str)-1;
                        for($k = 0;$k < 6;$k ++) // 字符串长度为6
                        { 
                            $num1 = mt_rand(0, $len); 
                            $name .= $str[$num1]; 
                        }
                    }

                    $data['user_pass'] = $_POST['password']?$_POST['password']:"123456"; // 密码默认 123456
                    $data['user_encrypt'] = gen_random_string(6);
                    $data['user_pass'] = $Model->hashPassword($data['user_pass'], $data['user_encrypt']);
                    $data['user_name'] = $prex.$name;
                    $data['nick_name'] = $name;
                    $data['reg_time'] = time();
                    $data['farm_name'] = $name . '_farm';
                    $data['is_platform'] = 1;


                    $tem_user_names[] = $name;
                    // var_dump($data);

                    M('user')->data($data)->add();

                }

                $this->success('批量添加平台用户成功',U('index'));
                
                // var_dump($data);die;
            }
            else 
            {
                $this->error('新增失败,请填写数量');
            }
            
        } 
        else 
        {
            

            $this->meta_title = '批量添加平台用户';
            $this->display();
        }  
    }

}