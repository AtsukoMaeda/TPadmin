<?php
namespace Admin\Controller;

class MenuController extends BaseController{

	//检验是否开启开发者模式
    public function __construct(){
    	parent::__construct();
		if(!C('OPEN_UP'))
			die('访问错误！');   	
    }


	//增加菜单
	public function menuAdd(){
		if(IS_AJAX){
			$menu=D('menu');
			$data=$menu->validate($menu->validate)->create(I('post.'));
			if($data){
				$menu->add();
				$this->ajaxReturn(array());
			}else{
				$this->ajaxReturn($menu->getError());
			}
			die;
		}
		$menu=M('menu');
		$data=$menu->select();
		$data=$this->treeSort($data);
		$this->assign('list',$data);
		$this->display();
	}

	//获取菜单内容
	public function menuList(){
		$menu=M('menu');
		$display=['<d style="color:red;">隐藏</d>','显示'];
		$data=$menu->select();
		$data=$this->treeSort($data,'menuid');
		$this->assign('display',$display);
		$this->assign('list',$data);
		$this->display();
	}

	//对菜单进行排序
	public function menuSort(){
		$this->listSort('menu','menuid');
	}

	//修改菜单显示与隐藏
	public function dc(){
		if(IS_AJAX){
			$data=I('post.');
			$display=[1,0];
			if(M('menu')->where("menuid={$data['menuid']}")->setField('display',$display[$data['display']])){
				echo '1';die;
			}else{
				echo '2';die;
			}
		}
	}

	//删除菜单
	public function menuDelete(){
		if(IS_AJAX){
			$menuid=I('post.menuid');
			$menu=M('menu');
			$menuidArr=$menu->field('menuid,parentid')->select();
			$deleteArr=$this->searchChild($menuidArr,$menuid,'menuid');
			$deleteArr[]=$menuid;
			$delete=implode(',',$deleteArr);
			if($menu->where(array('menuid'=>array('in',$delete)))->delete()){
				echo 1;
			}else{
				echo 2;
			}
		}
	}

	//获取某栏目信息
	public function getInfo(){
		if(IS_AJAX){
			$menuid=I('post.menuid');
			$menu=M('menu');
			$info=$menu->where(array('menuid'=>$menuid))->find();
			$data=$menu->select();
			$data=$this->treeSort($data,'menuid');
			$this->assign('list',$data);
			$this->assign('info',$info);
			$this->display('updateform');
		}
	}

	//更新栏目
	public function menuUpdate(){
		if(IS_AJAX){
			$data=I('post.');
			if($flag=M('menu')->save($data)){
				echo 1;
			}else{
				echo 2;
			}
		}
	}

	//增加子类栏目或者父类栏目
	public function addChild(){
		if(IS_AJAX){
			$menu=D('menu');
			$data=$menu->validate($menu->validate)->create(I('post.'));
			if($data&&$menu->add()){
				$this->ajaxReturn(array());
			}else{
				$this->ajaxReturn($menu->getError());
			}
		}
	}
}