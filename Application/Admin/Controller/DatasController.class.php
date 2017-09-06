<?php
namespace Admin\Controller;

class DatasController extends BaseController{

	public function dataList(){
		$dataname=C('DB_NAME');
		$data=M();
		$list=$data->query('show tables');
		$path='./Uploads/file/datasinfo.php';
		$datasinfo=include($path);
		// $datasinfo=[];
		// $list=$data->query('show table status like "tp_admin"');
		foreach ($list as $k => $v) {
			$s=$v['tables_in_'.$dataname]?$v['tables_in_'.$dataname]:$v['Tables_in_'.$dataname];
			$comment=$data->query('show table status like "'.$s.'"');
			$list[$k]['comment']=$comment[0]['comment']?$comment[0]['comment']:$comment[0]['Comment'];
			$list[$k]['update_time']=$comment[0]['update_time']?$comment[0]['update_time']:$comment[0]['Update_time'];
			$list[$k]['download_time']=$datasinfo[$comment[0]['name']]['download_time']?$datasinfo[$comment[0]['name']]['download_time']:$datasinfo[$comment[0]['Name']]['download_time'];
			$list[$k]['upload_time']=$datasinfo[$comment[0]['name']]['upload_time']?$datasinfo[$comment[0]['name']]['upload_time']:$datasinfo[$comment[0]['Name']]['upload_time'];
			// $datasinfo[$comment[0]['name']]['download_time']=time();
			// $datasinfo[$comment[0]['name']]['upload_time']=time();
		}
		// $datasinfo=var_export($datasinfo,true);
		// file_put_contents($path,'<?php return '.$datasinfo.'; ? >');
		$this->assign('list',$list);
		$this->assign('dataname',$dataname);
		$this->display();
		// dump($list);
	}

	public function download($table){
		//运行mysqldump备份数据库表
		set_time_limit(0);
		$path=__PATH__;
		$filename=$table.date('-Y-m-d',time()).'.sql';
		$php=C('MYSQLDUMP_PATH');
		$host=C('DB_HOST');
		$user=C('DB_USER');
		$pwd=C('DB_PWD');
		$db=C('DB_NAME');
		$port=C('DB_PORT');
		$do='-h'.$host.' -u'.$user.' -p'.$pwd.' -P'.$port.' '.$db.' '.$table.' > '.$path.'/Uploads/file/'.$filename;
		exec($php.' '.$do,$back,$return);
		if($return==0){
		$path='./Uploads/file/datasinfo.php';
		$datasinfo=include($path);
		$datasinfo[$table]['download_time']=time();
		$datasinfo=var_export($datasinfo,true);
		file_put_contents($path,"<?php \n return ".$datasinfo."; ?>");
		$this->log_add(['content'=>sprintf('[%s]备份数据成功',session('admin'))]);
		header("Content-Type:application/force-download");
		header("Content-Disposition:attachment;filename=".$filename);
		readfile('./Uploads/file/'.$filename);
		unlink('./Uploads/file/'.$filename);
		}
		// $model=M();
		// $flag=$model->query("select * from tp_admin into outfile 'c:/database/akb.xls'");
		// // $filename='webinfo.php';
	}

	public function upload(){
		if(IS_POST){
			$table=I('post.table');
			if($_FILES['sql']['size']>0){
				set_time_limit(0);
				$path=__PATH__;
				$filename=$path.'/Uploads/file/upload'.time().'.sql';
				move_uploaded_file($_FILES['sql']['tmp_name'],$filename);
				$php=C('MYSQL_PATH');
				$host=C('DB_HOST');
				$user=C('DB_USER');
				$pwd=C('DB_PWD');
				$db=C('DB_NAME');
				$port=C('DB_PORT');
				$do='-h'.$host.' -u'.$user.' -p'.$pwd.' -P'.$port.' '.$db.' <'.$filename;
				//$do='-hlocalhost -uroot -ps9787531 ad < C:\Users\Administrator\Desktop\tp_fenxian-2017-08-22(1).sql';
				exec($php.' '.$do,$back,$return);
				unlink($filename);
				if($return==0){
				$path='./Uploads/file/datasinfo.php';
				$datasinfo=include($path);
				$datasinfo[$table]['upload_time']=time();
				$datasinfo=var_export($datasinfo,true);
				file_put_contents($path,'<?php return '.$datasinfo.'; ?>');
				$this->log_add(['content'=>sprintf('[%s]恢复数据成功',session('admin'))]);
				$this->success('导入数据成功！');
				}else{
				$this->success('导入数据失败！');
				}
			}
		}
	}
}