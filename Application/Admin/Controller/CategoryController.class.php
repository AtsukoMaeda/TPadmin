<?php
namespace Admin\Controller;

class CategoryController extends BaseController{
	
	public function categoryAdd(){
		if(!C('OPEN_UP'))
			die('访问错误！');
		$category=M('category');
		if(IS_AJAX){
			$post=I('post.');
			if($post['category_name']==''){
				$return['category_name']='栏目名不能为空';
			}
			if($category->where("category_name='{$post['category_name']}'")->find()){
				$return['category_name']='该栏目已经存在';
			}
			if($post['action']==''){
				$return['action']='方法名不能为空';
			}
			if($post['controller']==''){
				$return['controller']='控制器名不能为空';
			}
			if(empty($return)&&$category->add($post)){
				$this->log_add(['content'=>sprintf('[%s]增加栏目成功',session('admin'))]);
				echo 1;
			}else{
				$this->ajaxReturn($return);
			}
			die;
		}
		$data=$this->treeSort($category->select(),'id');
		$this->assign('list',$data);
		$this->display();
	}

	public function categoryList(){
		$category=M('category');
		$display=['<d style="color:red;">隐藏</d>','显示'];
		$list=$category->select();
		$list=$this->treeSort($list);
		$this->assign('display',$display);
		$this->assign('list',$list);
		$this->display();
	}

	public function categorySort(){
		$this->listSort('category','id');
	}

	public function dc(){
		if(IS_AJAX){
			$data=I('post.');
			$display=[1,0];
			if(M('category')->where("id={$data['id']}")->setField('display',$display[$data['display']])){
				echo '1';die;
			}else{
				echo '2';die;
			}
		}
	}

	public function categoryDelete(){
		if(IS_AJAX){
			$menuid=I('post.id');
			$menu=M('category');
			$menuidArr=$menu->field('id,parentid')->select();
			$deleteArr=$this->searchChild($menuidArr,$menuid);
			$deleteArr[]=$menuid;
			$delete=implode(',',$deleteArr);
			if($menu->where(array('id'=>array('in',$delete)))->delete()){
				$this->log_add(['content'=>sprintf('[%s]删除栏目成功',session('admin'))]);
				echo 1;
			}else{
				echo 2;
			}
		}
	}

	//获取某栏目信息
	public function getInfo(){
		if(IS_AJAX){
			$menuid=I('post.id');
			$menu=M('category');
			$info=$menu->where(array('id'=>$menuid))->find();
			$data=$menu->select();
			$data=$this->treeSort($data);
			$this->assign('list',$data);
			$this->assign('info',$info);
			$this->display('updateform');
		}
	}

	public function categoryUpdate(){
		if(IS_AJAX){
			$category=D('category');
			$data=$category->validate($category->validate)->create(I('post.'));
			if($data){
				if($flag=$category->save()){
					$this->log_add(['content'=>sprintf('[%s]更新栏目成功',session('admin'))]);
					echo 1;
				}elseif($flag==0){
					echo 3;
				}else{
					echo 2;
				}
			}else{
				$this->ajaxReturn($category->getError());
			}
		}
	}

	public function addChild(){
		if(!C('OPEN_UP'))
			return false;
		if(IS_AJAX){
			$menu=D('category');
			$data=$menu->validate($menu->validate1)->create(I('post.'));
			if($data&&$menu->add()){
				$this->log_add(['content'=>sprintf('[%s]增加栏目成功',session('admin'))]);
				$this->ajaxReturn(array());
			}else{
				$this->ajaxReturn($menu->getError());
			}
		}
	}
}