<?php
namespace Admin\Model;
use Think\Model;
class CategoryModel extends Model {
	protected $patchValidate = true;//批量验证
	//用户注册接收的表单
	protected $_insertFields=array('parentid','category_name','controller','action','display','listorder,id');
	//栏目修改增加验证规则
	public $validate=array(
		array('parentid','require','父级栏目必须选',1),
		array('category_name','require','栏目名必须填写',1), 
		array('controller','require','控制器名必须填写',1),
		array('action','require','方法名必须填写',1),
		array('listorder','require','排序必须填写',1),
		array('controller','catest','该控制器与方法组合已存在',1,'callback'),
		array('action','catest','该控制器与方法组合已存在',1,'callback'),
		);

	public $validate1=array(
		array('parentid','require','父级栏目必须选',1),
		array('category_name','require','栏目名必须填写',1), 
		array('controller','require','控制器名必须填写',1),
		array('action','require','方法名必须填写',1),
		array('controller','catest1','该控制器与方法组合已存在',1,'callback'),
		array('action','catest1','该控制器与方法组合已存在',1,'callback'),
		);

	protected function catest(){
		$c=I('post.controller');
		$a=I('post.action');
		$id=I('post.id');
		if(M('category')->where(['controller'=>['eq',$c],'action'=>['eq',$a],'id'=>['neq',$id]])->find()){
			return false;
		}else{
			return true;
		}
	}
	protected function catest1(){
		$where['controller']=I('post.controller');
		$where['action']=I('post.action');
		if(M('category')->where($where)->find()){
			return false;
		}else{
			return true;
		}
	}
}
