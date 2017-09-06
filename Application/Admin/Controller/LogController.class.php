<?php
namespace Admin\Controller;

class LogController extends BaseController{

	public function logList(){
		$css=array(
			'prev'=>'上一页',
			'next'=>'下一页',
			'first'=>'首页',
			'last'=>'末页',
			'theme'=>'%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%'
			);
		$model=M('adminLog');
		$count=$model->count();//总记录数
		$page=new \Think\AjaxPage($count,48,'mypage');
		$page->lastSuffix=false;
		$page->rollPage=7;
		if($css){
		foreach ($css as $k => $v) {
		$page->setConfig($k,$v);
		}
		}
		$startno=$page->firstRow;//起始行数
		$pagesize=$page->listRows;//页面大小
		$data=$model->order('add_time DESC')->limit("$startno,$pagesize")->select();
		$pagestr=$page->show();//组装分页字符串
		$this->assign('list',$data);
		$this->assign('pages',$pagestr);

		// $data=page('adminLog',7,48,null,'add_time DESC',$css);
		// $this->assign('list',$data['data']);
		// $this->assign('pages',$data['pages']);
		if(IS_AJAX){
			$this->display('ajaxpage');
			die;
		}
		$this->display();
	}
}