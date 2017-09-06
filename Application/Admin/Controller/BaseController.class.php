<?php
namespace Admin\Controller;
use Think\Controller;
class BaseController extends Controller{

        protected $AdminLog;

    public function _initialize(){
        $webinfo=include('./Uploads/file/webinfo.php');
        $this->assign('webinfo',$webinfo);
        $this->AdminLog = D('AdminLog');
    	if(!session('admin')){
    		if($admin_online=cookie('admin_online')){
    			$admin=M('admin');
    			if($data=$admin->field('username,id')->where("online='{$admin_online}'")->find()){
                                        cookie('loginInfo',$admin->field('login_ip,login_address,login_time')->find($data['id']));
    				session('admin',$data['username']);
    				session('adminid',$data['id']);
                    $this->AdminLog->log_add(array('content'=>sprintf('[%s]登录成功！',$data['username'])));
                    $update=[];
                    $update['login_ip']=getip();
                    $update['login_address']=getIpAddress();
                    $update['login_time']=time();
                    $update['id']=1;
                    $admin->save($update);
                    unset($data);
    			}
    		}   		
    	}

        if(!session('admin')){
            redirect(U('Admin/Login/index'), 2,'请登录...');
        }
        
        $this->mid=session('adminid');
    	if(C('CHECK_ROOT')){
        	if(session('adminid') != 1){
        		if(!$this->checkRoot()){
                  $this->error("你无此操作权限!",U('Index/index'),2);
                }
        	}
        }
        //取出后台栏目
        $menu=M('menu');
        $roleid=M('admin')->where("id={$this->mid}")->getField('roleid');
        $role=M('role');
        $allow=$role->where("roleid={$roleid}")->getField('allow');
        if($allow==''){
            if($this->mid==1){
                $data=$menu->where('display=1')->order('listorder ASC')->select();
                $data=$this->treeArr($data);
            }else{
                $data='';
            }
        }else{
            $data=$menu->where("menuid in({$allow}) AND display=1")->order('listorder ASC')->select();
            $data=$this->treeArr($data);  
        }
        $this->bread();
        $this->assign('left',$data);
    }

    public function log_add($content)
    {
        $this->AdminLog->log_add($content);
    }
    
    public function treeSort($arr,$id='id',$level=0,$deep=0){
        static $return=[];
        foreach($arr as $k=>$v){
            if($v['parentid']==$level){
                $v['deep']=$deep;
                $return[]=$v;
                $this->treeSort($arr,$id,$v[$id],$deep+1);
            }
        }
        return $return;
    }

    public function treeArr($arr){
        $return=[];
        $out=[];
        foreach($arr as $k => $v){
            if($v['parentid']==0){
                $return[$v['menuname'].'|'.$v['i']]=[];
                $out[$v['menuid']]=$v['menuname'].'|'.$v['i'];
                unset($arr[$k]);
            }
        }
        foreach($arr as $k => $v){
            if(is_array($return[$out[$v['parentid']]])){
                $return[$out[$v['parentid']]][]=$v;
            }   
        }
        return $return;
    }

    public function checkRoot(){
        if(MODULE_NAME == 'Admin' && CONTROLLER_NAME == 'Index' && ACTION_NAME=='index'){
            return true;
        }
        if(MODULE_NAME == 'Admin' && CONTROLLER_NAME == 'Menu'){
            return true;
        }
        $data = [];
        $data['m'] = MODULE_NAME;
        $data['c'] = CONTROLLER_NAME;
        $data['action'] = ACTION_NAME;
        $menu=M('menu');
        $role=M('role');
        $menuid=$menu->where($data)->getField('menuid');
        $roleid=M('admin')->where("id={$this->mid}")->getField('roleid');
        $allow=$role->where("roleid={$roleid}")->getField('allow');
        if($allow==''){
            return false;
        }
        $allow=explode(',',$allow);
        if(in_array($menuid,$allow)){
            return true;
        }else{
            return false;
        }
    }

    public function bread(){
            $c=CONTROLLER_NAME;
        if($c!='Index' && $c!='Menu'){
            $m=MODULE_NAME;
            $a=ACTION_NAME;
            $menu=M('menu');
            $id=$menu->where("c='{$c}' and action='{$a}'")->getField('menuid');
            if(!$id){
                return false;
            }
            $stop=1;
            $return=[];
            $arr=$menu->select();
            while($stop!=0){
                foreach($arr as $k => $v){
                    if($v['menuid']==$id){
                        $return[]=$v['menuname'];
                        $id=$v['parentid'];
                        $stop=$id;
                    }
                }
            }
            $this->assign('nowAction',$return[0]);
            $return=array_reverse($return);
            $this->assign('bread',$return);
        }
    }

    protected function listSort($model,$id){
        if(IS_POST){
            $data=I('post.');
            $times=0;
            foreach($data as $k => $v){
                $arr=explode('-',$k);
                if($v!=$arr[1]){
                    if(M($model)->where("{$id}={$arr['0']}")->setField('listorder',$v)){
                        $times++;
                    }
                }
            }
            if($times){
                $this->success("成功修改了{$times}个记录！");
            }else{
                $this->error('没有一个记录被修改！');
            }
        }
    }

    //寻找所有子代
    public function searchChild($arr,$menuid,$id='id'){
        static $return=[];
        foreach($arr as $k=>$v){
            if($v['parentid']==$menuid){
                $return[]=$v[$id];
                $this->searchChild($arr,$v[$id],$id);
            }
        }
        return $return;
    }
}