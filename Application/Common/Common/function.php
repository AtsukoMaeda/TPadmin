<?php
//文件上传类（可以设置多个参数）
/**
 * @$file 上传的文件
 * @$maxSize 上传文件大小
 * @$exts 上传的文件类型
 * @$is_thumb array 是否生成缩略图,数组内是缩略图参数
 * @$savePath 保存路径，区别于更目录路径
 * @return $info array $info['savepath'].$info['savename']图片保存路径，$info['thumb']，缩略图保存路径
 */
function upload($file=null,$exts=0,$is_thumb=false,$maxSize=0,$savePath='')
{
    //调用
    $upload = new \Think\Upload();// 实例化上传类
    $upload->maxSize   = $maxSize;// 设置附件上传大小
    $upload->exts      = $exts; //array('jpg', 'gif', 'png', 'jpeg'); 设置附件上传类型
    $upload->savePath  = $savePath; // 设置附件上传目录
  // 上传文件
    //如果单个文件还是多个文件
    if($file){
      $info = $upload->uploadOne($file);
    }else{
    $info = $upload->upload();
    }
    //判定是否文件上传成功de
    if(!$info) {
  // 上传错误提示错误信息
    //print_r($upload->getError());die;
    //$this->error($upload->getError());
    return false;
    }else{
      if($is_thumb){
      $img=new \Think\Image();
      $big_img=$upload->rootPath.$info['savepath'].$info['savename'];
      $img->open($big_img);
      $img->thumb($is_thumb[0],$is_thumb[1]);
      $thumb=$upload->rootPath.$info['savepath'].'small'.$info['savename'];
      $img->save($thumb);
          $info['thumb']=$info['savepath'].'small'.$info['savename'];
    }
    return $info;
  }
}

//上传图片
function fab_upload($files ,$maxSize = 0,$exts = null,$savePath = '')
{
        //判定文件信息是否为空
    if(empty($files)){
        return false;
    }
    if($exts === null){
        $exts = array('jpg', 'gif', 'png', 'jpeg');
    }else{
        $exts = 0;
    }
    $tmp = array();
            //将文件信息（数组）用foreach循环遍历，
    foreach($files as $k => $v){
                //判定文件大于0之后，将遍历value作为参数传入upload方法
        if($v['size'] > 0){

            $res = upload($v,$maxSize,$exts,$savePath);
                            //如果传入成功就会将文件存储路径传入数组$tmp[]之中
            if($res){
                $tmp[$k] = $res['savepath'].$res['savename'];
            }
        }
    }
            //将存储传入文件路径的数组return回去
    return $tmp;
}


//获取现有url的所有参数，并把重复的替换
function getUrlvalue($key,$value,$stop = array())
{
	$data = array();
	foreach($_GET as $k=>$v){
        if(!in_array($k,$stop)){
            $data[$k] = $v;
        }
	}
	$data[$key] = $value;
	return $data;
}

//获取现有url的所有参数，并把重复的替换
function get_url_value($arr = array(),$stop = array())
{
    $get = $_GET;
    $new_data = array();
    foreach($get as $k => $v){
        if(!in_array($k,$stop)){
            $new_data[$k] = $v;
        }
    }
    if(count($arr) > 0){
        $new_data = array_merge($arr,$new_data);
    }
    return $new_data;
}

//获取ip
function getip()
{
	return get_client_ip();
}

//获取ip所属地址
function getIpAddress($ip)
{
	$Ip = new \Org\Net\IpLocation('UTFWry.dat'); // 实例化类 参数表示IP地址库文件
	$address = $Ip->getlocation($ip); // 获取某个IP地址所在的位置
	$address = iconv('gb2312','utf-8',$address['country']);
	return $address;
}

//检查密码是否一致
function check_password($inputpassword,$userpassword)
{
	if(crypt($inputpassword,$userpassword) == $userpassword){
		return true;
	}else{
		return false;
	}
}

//验证码检验
function check_verify($code,$id=true)
{
    if($id==false){
           $config =array(
                     'reset'     =>  false, // 验证成功后是否重置
                                       );
           $verify = new \Think\Verify($config);
    }else{
           $verify=new \Think\Verify();
    }
    return $verify->check($code);
}

//加密
function pwd_encode($data,$key,$expire)
{
	$key=$key?$key:C('PWD_KEY');
	$expire=$expire?$expire:C('AUTO_LOGIN_TIME');
	return \Think\Crypt\Driver\Think::encrypt($data,$key,$expire);
}
//解密
function pwd_decode($data)
{
	$key=$key?$key:C('PWD_KEY');
	return \Think\Crypt\Driver\Think::decrypt($data,$key);
}

//读取Excel
function Read_excel($file){
  import('Com.PHPExcel');       //引入excel
  import('Com.PHPExcel.Reader.Excel2007');
  $Excel = new \PHPExcel();
  $Reader = new \PHPExcel_Reader_Excel2007();
  if(!$Reader->canRead($file)){
      import('Com.PHPExcel.Reader.Excel5');
      $Reader = new \PHPExcel_Reader_Excel5();
      if(!$Reader->canRead($file)){
          exit("Excel 操作出错!");
      }
  }
  $PHPExcel = $Reader->load($file);
  $SheetCount = $PHPExcel->getSheetCount();   // 获取工作表个数
  $array = array();
  for($i=0;$i<$SheetCount;$i++){
      $currentSheet = $PHPExcel->getSheet($i);    //获取第$i个工作表
      $allColumn = $currentSheet->getHighestColumn();
      $allRow = $currentSheet->getHighestRow();
      $array[$i]["Title"] = $currentSheet->getTitle();
      $array[$i]["Cols"] = $allColumn;
      $array[$i]["Rows"] = $allRow;
      $arr = array();
      //循环获取表中的数据，$currentRow表示当前行，从哪行开始读取数据，索引值从0开始
      for($currentRow=1;$currentRow<=$allRow;$currentRow++){
          for($currentColumn='A';$currentColumn<=$allColumn;$currentColumn++){
              $address=$currentColumn.$currentRow;
              $arr[$currentRow][$currentColumn]=$currentSheet-> getCell($address)->__toString();
          }
      }
      $array[$i]["Content"] = $arr;
  }
  return $array;
}

//发送邮箱
function SendEmail($email,$title,$content,$webname)
{
  import('Com.PHPMailer');
  $mail = new \PHPMailer();
  $mail->IsSMTP();                           // tell the class to use SMTP
  $mail->SMTPAuth   = true;                  // enable SMTP authentication
  $mail->Port       = 25;                    // set the SMTP server port
  $mail->CharSet    = 'UTF-8'; //设置邮件的字符编码，这很重要，不然中文乱码
  $mail->Host       = "smtp.163.com"; // SMTP server
  $mail->Username   = "lufei290276578@163.com";     // SMTP server username
  $mail->Password   = "s9787531";            // SMTP server password
  //$mail->IsSendmail();  // tell the class to use Sendmail
  $mail->AddReplyTo("lufei290276578@163.com","{$webname}");
  $mail->From       = "lufei290276578@163.com";
  $mail->FromName   = "{$webname}";
  $mail->Subject  = $title;
  $mail->AltBody    = $title; // optional, comment out and test
  $mail->WordWrap   = 80; // set word wrap
  $mail->MsgHTML($content);
  $mail->IsHTML(true); // send as HTML
  $mail->AddAddress($email);
  if($mail->Send()){
    return true;
  }else{
    return false;
  }
}

function p_del($input,&$model,$where,$field)
{
  if($input && is_array($input)){
    foreach($input as $id){
      $res = $model->field($field)->where($where,$id)->find();
      if($res){
        foreach($res as $v){
          $p = C('UNLINK_PATH').$v;
          if($v && file_exists($p)){
            unlink($p);
          }
        }
      }
      $model->where($where,$id)->delete();
    }
    return true;
  }else{
    return false;
  }
}

/*
文件下载
*/

function down_load($file,$down_load_file_name = '下载时的名字.txt')
{
    $fp = fopen($file,'rb');
    $fileName = date('Ymd-',time()).mt_rand(1,9999).$down_load_file_name;
    if(!$fp){
        header('HTTP/1.1 404 Not Found');
        echo "Error: 404 Not Found.(server file path error)<!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding --><!-- Padding -->";
        exit;
    }
    $encoded_filename = urlencode($fileName);
    $encoded_filename = str_replace("+", "%20", $encoded_filename);
    header('HTTP/1.1 200 OK');
    header( "Pragma: public" );
    header( "Expires: 0" );
    header("Content-type: application/octet-stream");
    header("Content-Length: ".filesize($file));
    header("Accept-Ranges: bytes");
    header("Accept-Length: ".filesize($file));
    $ua = $_SERVER["HTTP_USER_AGENT"];
    if (preg_match("/MSIE/", $ua)) {
        header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
    } else if (preg_match("/Firefox/", $ua)) {
        header('Content-Disposition: attachment; filename*="utf8\'\'' . $fileName . '"');
    } else {
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
    }
    ob_start();
    ob_clean();
    flush();
    fpassthru($fp);
}

//注册验证手机验证码
function checkSjyzm($sjyzm){
           $yzm=session('phone_yzm');
          if($sjyzm!=$yzm){
            return false;
          }
          return true;
}

//注册验证手机验证码
function checkPhone($phone){
           $phone1=session('phone');
          if($phone!=$phone1){
            return false;
          }
          return true;
}


//单表普通分页
function page($model,$rollpage,$pagesize,$where=null,$order=null,$css=array()){
    $model=M($model);
    $count=$model->where($where)->count();//总记录数
    $page=new \Think\Page($count,$pagesize);
    $page->lastSuffix=false;
    $page->rollPage=$rollpage;
    if($css){
      foreach ($css as $k => $v) {
      $page->setConfig($k,$v);
      }
    }
    $startno=$page->firstRow;//起始行数
    $pagesize=$page->listRows;//页面大小
    $data=$model->where($where)->order($order)->limit("$startno,$pagesize")->select();
    $pagestr=$page->show();//组装分页字符串
    $back['data']=$data;
    $back['pages']=$pagestr;
    return $back;
}

//网页链接整理不要协议
function url_sort($url){
  $preg='/\/\/(.*)/';
  preg_match($preg,$url,$new_url);
  return $new_url?$new_url[1]:$url;
}

function two_sort($arr,$index,$desc=0){
  $GLOBALS['index']=$index;
  $GLOBALS['desc']=$desc;
  function t_sort($a,$b){
  if($a[$GLOBALS['index']]==$b[$GLOBALS['index']]){
    return 0;
  }
  if($GLOBALS['desc']==0){
  return ($a[$GLOBALS['index']]<$b[$GLOBALS['index']])?-1:1;
    }else{
    return ($a[$GLOBALS['index']]>$b[$GLOBALS['index']])?-1:1;
    }
}
usort($arr,'t_sort');
unset($GLOBALS['index']);
unset($GLOBALS['desc']);
return $arr;
}

/**
 * @$time 时间戳
 * @$s 是否显示秒，默认不显示
 * @return 返回的准确时间
 */
function show_time($time,$s=null){
          $now=time();
          $today=strtotime(date("Y-m-d"));
          $todaycha=$now-$today;
          $cha=$now-$time;
          if($cha>=$todaycha&&$cha<$todaycha+3600*24){
               if($s){
                return '昨天 '.date('H:i:s',$time);
              }else{
                return '昨天 '.date('H:i',$time);
              }
          }elseif($cha>=0&&$cha<60){
            return '刚刚';
          }elseif($cha>=60&&$cha<3600){
            $return=floor($cha/60);
            return $return.'分钟前';
          }elseif($cha>=3600&&$cha<$todaycha){
            $return=floor($cha/3600);
            return $return.'小时前';
          }else if($cha>=$todaycha+3600*24&&$cha<$todaycha+3600*24*2){
            if($s){
              return '前天 '.date('H:i:s',$time);
            }else{
              return '前天 '.date('H:i',$time);
            }
          }else{
            if($s){
              return date('Y-m-d H:i:s',$time);
            }else{
              return date('Y-m-d H:i',$time);
            }
          }
}
?>
