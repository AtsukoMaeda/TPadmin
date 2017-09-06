<?php
namespace Admin\Model;
use Think\Model;

class AdminModel extends Model{
	protected $patchValidate = true;//批量验证

	protected $_insertFileds=['roleid','username','password'];

	public $validate=[
		['roleid','require','所属角色不能为空',1],
		['username','require','账号不能为空',1],
		['username','','该账号已经存在',1,'unique',3],
		['password','require','密码不能为空',1],
	];
}