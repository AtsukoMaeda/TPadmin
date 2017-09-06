<?php
namespace Admin\Controller;

class AdminController extends BaseController{

	public function adminAdd(){
		if(IS_AJAX){
			$post=I('post.');
			$admin=D('admin');
			$input=$admin->validate($admin->validate)->create($post);
			if($input){
				$input['password']=md5(md5(C('SAFE_KEYS').$input['password']));
				$input['inputtime']=time();
				if($admin->add($input)){
					$this->log_add(['content'=>sprintf('[%s]增加管理员成功',session('admin'))]);
					echo 1;
				}else{
					echo 2;
				}
			}else{
				$this->ajaxReturn($admin->getError());
			}
			die;
		}
		$role=M('role')->select();
		$this->assign('role',$role);
		$this->display();
	}

	public function adminList(){
		$role=M('role')->select();
		$data=M('admin')->alias('a')->join('join tp_role b on a.roleid=b.roleid')->field('a.username,b.rolename,a.inputtime,a.roleid,a.id')->select();
		$this->assign('list',$data);
		$this->assign('role',$role);
		$this->display();
	}

	public function roleAdd(){
		if(IS_AJAX){
			$data=I('get.');
			$role=M('role');
			if($data['rolename']==''){
				echo 4;die;
			}
			if($role->where('rolename="%s"',$data['rolename'])->find()){
				echo 3;die;
			}
			if($role->add($data)){
				$this->log_add(['content'=>sprintf('[%s]增加角色成功',session('admin'))]);
				echo 1;
			}else{
				echo 2;
			}
			die;
		}
		$this->display();
	}

	public function roleList(){
		$role=M('role');
		$admin=M('admin');
		$data=$role->select();
		foreach ($data as $k => $v) {
			$data[$k]['count']=$admin->where("roleid={$v['roleid']}")->count();
		}
		$this->assign('list',$data);
		$this->display();
	}

	public function adminUpdate(){
		if(IS_AJAX){
			$post=I('post.');
			if(session('adminid')!=1&&$post['id']==1){
				echo 3;die;
			}
			if(session('adminid')!=1&&$post['roleid']==1){
				echo 4;die;
			}
			if($post['password']){
				$post['password']=md5(md5(C('SAFE_KEYS').$post['password']));
			}else{
				unset($post['password']);
			}
			foreach ($post as $k => $v) {
				if($v==''){
					unset($post[$k]);
				}
			}
			if(M('admin')->save($post)){
				if($post['id']==$this->mid){
					session('admin',$post['username']);
				}
				$this->log_add(array('content'=>sprintf('[%s]修改管理员信息成功！',session('admin'))));
				echo 1;die;
			}else{
				echo 2;

			}
		}
	}

	public function adminDel(){
		if(IS_AJAX){
			$id=I('get.id');
			if($id==1){
				echo 3;die;
			}
			$admin=M('admin');
			if($admin->delete($id)){
				$this->log_add(['content'=>sprintf('[%s]删除管理员成功',session('admin'))]);
				echo 1;
			}else{
				echo 2;
			}
		}
	}

	public function roleSet($roleid=''){
		$role=M('role');
		if(IS_AJAX){
			$post=I('post.');
			if($post['roleid']==1){
				echo 3;die;
			}
			$post['setarr']=substr($post['setarr'], 0,strlen($post['setarr'])-1);
			if($role->where("roleid={$post['roleid']}")->setField('allow',$post['setarr'])){
				$this->log_add(['content'=>sprintf('[%s]设置权限成功',session('admin'))]);
				echo 1;
			}else{
				echo 2;
			}
			die;
		}
		$rolearr=explode(',', $role->where("roleid={$roleid}")->getField('allow'));
		$rolename=$role->where("roleid={$roleid}")->getField('rolename');
		$menu=M('menu');
		$data=$menu->select();
		$data=$this->treeSort($data,'menuid');
		$this->assign('rolearr',$rolearr);
		$this->assign('list',$data);
		$this->assign('rolename',$rolename);
		$this->display();
	}

	public function roleUpdate(){
		if(IS_AJAX){
			$post=I('post.');
			if(session('adminid')!=1&&$post['roleid']==1){
				echo 4;die;
			}
			if($post['rolename']==''){
				echo 3;die;
			}
			if(M('role')->save($post)){
				echo 1;
			}else{
				echo 2;
			}
		}
	}

	public function roleDel(){
		if(IS_AJAX){
			$roleid=I('get.roleid');
			if($roleid==1){
				echo 3;die;
			}
			if(M('role')->delete($roleid)&&M('admin')->where("roleid={$roleid}")->delete()){
				$this->log_add(['content'=>sprintf('[%s]删除角色成功',session('admin'))]);
				echo 1;
			}else{
				echo 2;
			}
		}
	}

	public function pwdUpdate(){
		if(IS_AJAX){
			$post=I('post.');
			$return=[];
			if($post['npwd']!=$post['npwd2']){
				$return['npwd']='两次密码必须相同';
				$return['npwd2']=$return['npwd'];
			}elseif($post['npwd']==''){
				$return['npwd']='新密码不能为空';
				$return['npwd2']=$return['npwd'];
			}
			$admin=M('admin');
			$pwd=$admin->where("id={$this->mid}")->getField('password');
			if(md5(md5(C('SAFE_KEYS').$post['pwd']))!=$pwd){
				$return['pwd']='密码不正确';
			}
			if(empty($return)){
				$npwd=md5(md5(C('SAFE_KEYS').$post['npwd']));
				if($admin->where("id={$this->mid}")->setField('password',$npwd)){
					$this->log_add(['content'=>sprintf('[%s]修改密码成功',session('admin'))]);
					echo 1;
				}else{
					echo 2;
				}
				die;
			}
			$this->ajaxReturn($return);
		}
	}
}