<?php
namespace Admin\Model;
use Think\Model;
class AdminLogModel extends Model {

	private $page = "";

	public function getList($pagesize=25){
		$where = '1';
		$tableName = $this->getTableName();

		if(!empty($_GET['stime'])){
			$stime = strtotime($_GET['stime']);
			$where .= " and ($tableName.`add_time` >= '$stime')";
		}

		if(!empty($_GET['etime'])){
			$stime = strtotime($_GET['etime']);
			$where .= " and ($tableName.`add_time` <= '$etime')";
		}

	
		$count = $this->where($where)->count();
	    $Page = new \Think\Page($count,$pagesize);
	    $this->page = $Page->show();
	    $limit = $Page->firstRow.','.$Page->listRows;
		return $this->query("select * from $tableName where $where order by`log_id` desc limit $limit ");
	}

	public function getPage(){
		return $this->page;
	}
	//记录日志
	public function log_add($input = array()){
		$data = array();
		$data['add_time'] = time();
		$data['username'] = session('admin');
		$data['ip'] = getip();
		$data = array_merge($data,$input);
		$this->add($data);
	}

}
