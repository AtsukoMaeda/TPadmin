<?php
namespace Admin\Controller;

class IndexController extends BaseController {
	
    public function index(){
    	if(!cookie('nowInfo')){
    		$loginInfo=M('admin')->field('roleid,login_ip,login_address,login_time')->find(session('adminid'));
    		$loginInfo['role']=M('role')->where('roleid=%d',$loginInfo['roleid'])->getField('rolename');
    		unset($loginInfo['roleid']);
    		cookie('nowInfo',$loginInfo);
    	}
    	$this->assign('nowInfo',cookie('nowInfo'));
        $this->display();
    }
}