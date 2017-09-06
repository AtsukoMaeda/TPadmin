<?php
namespace Admin\Model;
use Think\Model;
class MenuModel extends Model {
	protected $patchValidate = true;//批量验证
	//用户注册接收的表单
	protected $_insertFields=array('parentid','menuname','m','c','action','display','i','listorder');
	//栏目修改增加验证规则
	public $validate=array(
		array('parentid','require','父级栏目必须选',1),
		array('menuname','require','栏目名必须填写',1), 
		array('m','require','模块名必须填写',1), 
		array('c','require','控制器名必须填写',1),
		array('i','ci','顶级栏目必须有图标',1,'callback'),
		array('i','ci1','非顶级栏目不需要图标',1,'callback'),
		array('action','ca','顶级栏目不需要方法',1,'callback'),
		);

	protected function ci($i){
		if(I('post.parentid')==0 && $i=='')
			return false;
		else 
			return true;
	}

	protected function ca($action){
		if(I('post.parentid')==0 && $action!='')
			return false;
		else
			return true;
	}

	protected function ci1($i){
		if(I('post.parentid')!=0 && $i!='')
			return false;
		else 
			return true;
	}
}
