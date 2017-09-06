<?php
namespace Admin\Controller;

class SettingController extends BaseController{
	public function setMsg(){
		$path='./Uploads/file/webinfo.php';
		$webinfo=include($path);
		if(IS_POST){
			$post=I('post.');
			if($post['webtitle']==''){
				$return['webtitle']='网站名称必须填写';
			}
			if($post['webkey']==''){
				$return['webkey']='网站关键词必须填写';
			}
			if($post['webdes']==''){
				$return['webdes']='网站描述必须填写';
			}
			if($_FILES['logo']['size']>0){
				$info=upload($_FILES['logo']);
				if($info){
					unlink('./Uploads/'.$webinfo['logo']);
					$post['logo']=$info['savepath'].$info['savename'];
				}else{
					$return['logo']='图片上传失败';
				}
			}else{
				$post['logo']=$webinfo['logo'];
			}
			if(empty($return)){
				$post=var_export($post,true);
				if(file_put_contents($path,'<?php  return '.$post.'; ?>')){
					$this->log_add(['content'=>sprintf('[%s]修改网站信息成功',session('admin'))]);
					echo json_encode('');
				}
			}else{
				$return=json_encode($return);
				echo '<return>'.$return.'</return>';
			}
			die;
		}
		$this->display();
	}
}