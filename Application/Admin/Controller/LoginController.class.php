<?php
namespace Admin\Controller;
use Think\Controller;
class LoginController extends Controller {
    public function index(){
    	if(IS_POST){
    		$data=I('post.');
    		if(check_verify($data['checkword'],false)){
    			$admin=M('admin');
    			if($info=$admin->field('id,username,password')->where(array('username'=>$data['username']))->find()){
    				if($info['password']==md5(md5(C('SAFE_KEYS').$data['password']))){
    					if($data['online']){
    						$update['online']=md5($data['username'].time());
    					}
    					$update['login_ip']=getip();
    					$update['login_address']=getIpAddress();
						$update['login_time']=time();
						if($admin->where("id={$info['id']}")->save($update)){
							session('admin',$info['username']);
							session('adminid',$info['id']);
                            cookie('loginInfo',$admin->field('login_ip,login_address,login_time')->find($info['id']));
							cookie('admin_online',$update['online'],3600*24*10);
							$la = D('AdminLog');
				            $la->log_add(array('content'=>sprintf('[%s]登录成功！',$info['username'])));
				            echo json_encode(2);die;//登陆成功
						}
    				}else{
    					echo json_encode(1);die;//密码错误
    				}
    			}else{
    				echo json_encode(1);die;//账号错误
    			}
    		}else{
    			echo json_decode(3);die;//验证码错误
    		}
    		die;
    	}
        $this->display();
    }

    public function verify(){
        $config = array(
            'fontSize'    =>    35,   // 验证码字体大小
            'length'      =>    4,     // 验证码位数
            'useNoise'    =>    false, // 关闭验证码杂点
            'useCurve'    =>    false, // 关闭曲线干扰
        );
        $Verify = new \Think\Verify($config);
        $Verify->entry();
    }

    public function logout(){
        session(null);
        cookie('admin_online',null);
        $this->success('退出登录成功',U('Login/index'),1);
    }
}