<?php
/**
 * 作者：邢鸿标
 * 日期：2017-8-23
 * 数组操作类
 */
namespace Think;

class AtsukoArr{

	
	private $method=false;
	private $old;
	private $error=[];
	private $arr;
	private $return=[];
	private $where=[];
	private $whereand=[];

	public function __construct($arr){	
		$this->arr=$arr;
		$this->old=$arr;
	}

	public function __get($value){
		if($this->$value){
			return $this->$value;
		}
	}

	public function __set($key,$value){
		$this->$key=$value;
	}

	public function where($where){
		if(is_string($where)){
			$preg=['/!=/'=>'!','/<=/'=>'-','/>=/'=>'+'];
			foreach ($preg as $k=>$v) {
				$where=preg_replace($k,$v,$where);
			}
			if(preg_match_all('/ or /i',$where,$arr)){
				$whereall=$this->orall($where);
				$stor=$this->arr;
				$return=[];
				foreach ($whereall as $v) {
					$select=$this->where($v)->select();
					$this->arr=$stor;
					$return=array_merge($return,$select);
				}
				$this->arr=$return;
				$this->uniq();
				return $this;
			}elseif(preg_match_all('/ and /i',$where,$arr)){
				$whereall=$this->andall($where);
				$this->whereand=[];
				foreach ($whereall as $v) {
					$this->where($v)->select();
				}
				return $this;
			}
		}
		if(is_string($where)){
			$preg='/(.*) in\((.*)\)/i';
			$flag=preg_match($preg,$where,$arr);
			if($flag){
				$in=explode(',',$arr[2]);
				foreach ($this->arr as $v) {
					foreach ($in as $v1) {
						if($v[$arr[1]]==$v1){
							$this->return[]=$v;
						}
					}
				}
				$this->arr=$this->return;
				$this->return=[];
			}elseif($flag=preg_match('/(.*) notin\((.*)\)/i',$where,$arr)){
				$in=explode(',',$arr[2]);
				$stor=$this->arr;
				foreach ($stor as $k=>$v) {
					foreach ($in as $v1) {
						if($v[$arr[1]]==$v1){
							unset($stor[$k]);
						}
					}
				}
				$this->arr=[];
				foreach ($stor as $v) {
					$this->arr[]=$v;
				}
				$this->return=[];
			}
			// var_dump($where);
			// if($flag&&preg_match('/ and /i',$where)){
			// 	$where=preg_replace('/(.*) in\((.*)\) and /i','',$where);
			// 	$where=preg_replace('/(.*) notin\((.*)\) and /i','',$where);
			// }elseif($flag&&!preg_match('/ and /i',$where)){
			// 	return $this;
			// }
			if($flag){
				return $this;
			}
		}
		if(is_array($where)){
			for($i=0;$i<count($where);$i++){
				$this->where[]='=';
			}
		}
		$where=is_string($where)?$this->strToArr($where,' and '):$where;
		$times=0;
		foreach ($where as $k1 => $v1) {
			foreach ($this->arr as $v) {
				if($this->where[$times]=='='){
					if($v[$k1]==$v1){
						$this->return[]=$v;
					}
				}elseif($this->where[$times]=='>'){
					if($v[$k1]>$v1){
						$this->return[]=$v;
					}
				}elseif($this->where[$times]=='<'){
					if($v[$k1]<$v1){
						$this->return[]=$v;
					}
				}elseif($this->where[$times]=='!'){
					if($v[$k1]!=$v1){
						$this->return[]=$v;
					}
				}elseif($this->where[$times]=='\+'){
					if($v[$k1]>=$v1){
						$this->return[]=$v;
					}
				}elseif($this->where[$times]=='\-'){
					if($v[$k1]<=$v1){
						$this->return[]=$v;
					}
				}
			}
			$this->arr=$this->return;
			$this->return=[];
			$times++;
		}
		return $this;
	}

	public function order($order){	
		$order=is_string($order)?$this->strToArr($order,','):$order;
		$sort=null;
		foreach($order as $k=>$v){
			for($i=1;$i<count($this->arr);$i++){
				$age=$this->arr[$i][$k];
				$v1=$this->arr[$i];
				$j=$i-1;
				if($v=='desc'||$v=='DESC'){
					while($j>=0&&$this->arr[$j][$k]<$age){
						if(!$sort){
							$this->arr[$j+1]=$this->arr[$j];
							$j--;
						}elseif($this->arr[$j+1][$sort]==$this->arr[$j][$sort]){
							$this->arr[$j+1]=$this->arr[$j];
							$j--;
						}else{
							break;
						}
					}
				}elseif($v=='asc'||$v='ASC'){
					while($j>=0&&$this->arr[$j][$k]>$age){
						if(!$sort){
							$this->arr[$j+1]=$this->arr[$j];
							$j--;
						}elseif($this->arr[$j+1][$sort]==$this->arr[$j][$sort]){
							$this->arr[$j+1]=$this->arr[$j];
							$j--;
						}else{
							break;
						}
					}
				}
				$this->arr[$j+1]=$v1;
			}
			$sort=$k;
		}
		return $this;
	}

	private function strToArr($str,$p){
		$this->where=[];
		$e=['>','<','=',' ','!','\+','\-'];
		$preg='/(.*)'.$p.'(.*)/i';
		$flag=preg_match_all($preg,$str,$arr);
		$return=[];
		if($flag){
			unset($arr[0]);
			foreach ($arr as $v) {
				foreach ($e as $v1){
					$a=preg_match('/(.*)'.$v1.'(.*)/',$v[0],$fuhao);
					if($a){
						$this->where[]=$v1;
						$return[$fuhao[1]]=$fuhao[2];
						break;
					}
				}
			}
		}else{
			foreach ($e as $v) {
				$a=preg_match('/(.*)'.$v.'(.*)/',$str,$fuhao);
				if($a){
					$this->where[]=$v;	
					$return[$fuhao[1]]=$fuhao[2];
					break;
				}
			}
		}
		return $return;
	}

	public function setInc($field,$int=''){
		$this->math($field,$int,true);
		return $this;
	}

	public function setDec($field,$int=''){
		$this->math($field,$int);
		return $this;
	}

	public function math($field,$int='',$flag=false){
		if(is_string($field)){
			$field=explode(',',$field);
		}
		$int=$int==''?1:(int)($int);
		foreach ($this->arr as $k=>$v) {
			foreach ($v as $k1 => $v1) {
				foreach ($field as $v2) {
					if($k1==$v2){
						if($flag){
							$this->arr[$k][$k1] +=$int;
						}else{
							$this->arr[$k][$k1] -=$int;
						}
					}
				}
			}
		}
	}

	public function field($field){
		$this->method=true;
		if(is_string($field)){
			$field=explode(',',$field);
		}
		$return=[];
		foreach ($this->arr as $k1 => $v1) {
			$stor=[];
			foreach ($v1 as $k2 => $v2) {
				foreach ($field as $v) {
					if($k2==$v){
						$stor[$v]=$v2;
					}
				}
			}
			$return[]=$stor;
		}
		$this->arr=$return;
		return $this;
	}

	public function select(){
		return $this->arr;
	}

	public function find($id=''){
		if($id){
			if($id>count($this->arr)){
				$this->error['find']='输入查找的id大于结果集的最大数';
				return $this->getError();
			}
			return $this->arr[(int)($id)-1];
		}
		return $this->arr[0];
	}

	public function setField($key,$value='',$flag=false){
		if(!$flag&&is_string($key)){
			$this->arr[0][$key]=$value;
		}elseif(!$flag&&is_array($key)){
			if($value!==true){
				foreach ($key as $k => $v) {
					$this->arr[0][$k]=$v;
				}
			}elseif($value===true){
				foreach ($key as $k => $v) {
					foreach ($this->arr as $k1 => $v1) {
						$this->arr[$k1][$k]=$v;
					}
				}
			}
		}elseif(is_string($key)&&is_string($value)&&$flag){
			foreach ($this->arr as $k => $v) {
				$this->arr[$k][$key]=$value;
			}
		}
		return $this;
	}

	public function getField($field,$flag=false){
		if(!$flag){
			if(is_string($field)&&!preg_match('/,/',$field)){
				return $this->arr[0][$field];
			}else{
				if(is_string($field)){
					$field=explode(',',$field);
				}
				$return=[];
				foreach ($this->arr[0] as $k => $v) {
					foreach ($field as $v1) {
						if($k==$v1){
							$return[$k]=$v;
						}
					}
				}
				return $return;
			}
		}else{
			$return=[];
			$end=[];
			if(is_string($field)&&!preg_match('/,/',$field)){
				foreach ($this->arr as $v) {
					$return[]=$v[$field];
				}
				if(is_int($flag)){
					$i=0;
					foreach ($return as $v){
						if($i==$flag){
							break;
						}
						$end[]=$v;
						$i++;
					}
					$return=$end;
				}
				return $return;
			}else{
				if(is_string($field)){
					$field=explode(',',$field);
				}
				foreach ($this->arr as $v) {
					foreach ($v as $k1 => $v1) {
						foreach ($field as $v2) {
							if($k1==$v2){
								$return[$k1]=$v1;
							}
						}
					}
					$end[]=$return;
					$return=[];
				}
				if(is_int($flag)){
					$i=0;
					foreach ($end as $v){
						if($i==$flag){
							break;
						}
						$return[]=$v;
						$i++;
					}
					$end=$return;
				}
				return $end;
			}
		}
	}

	public function add($arr,$type=true){
		if(!is_array($arr)){
			$this->error['add']='输入的参数必须是一维数组或者二维数组';
			return $this;
		}
		$key=[];
		foreach ($this->arr[0] as $k => $v) {
			$key[]=$k;
		}
		if(count($arr)==count($arr,1)){
			$add=[];
			foreach ($arr as $k => $v) {
				foreach ($key as $v1) {
					if($k===$v1){
						$add[$k]=$v;
					}
				}
			}
			if(!empty($add)){
				if((count($add)!=count($this->arr[0]))&&$type){
					$new=[];
					foreach ($key as $v) {
						if(isset($add[$v])){
							$new[$v]=$add[$v];
						}else{
							$new[$v]='';
						}
					}
					$add=$new;
					$this->arr[]=$add;
				}elseif(count($add)==count($this->arr[0])){
					$this->arr[]=$add;
				}
			}
		}else{
			foreach ($arr as $k => $v) {
				$add=[];
				if(is_array($v)){
					foreach ($v as $k1 => $v1) {
						foreach ($key as $v2) {
							if($k1===$v2){
								$add[$k1]=$v1;
							}
						}
					}
				}
				if(!empty($add)){
					if((count($add)!=count($this->arr[0]))&&$type){
						$new=[];
						foreach ($key as $v) {
							if(isset($add[$v])){
								$new[$v]=$add[$v];
							}else{
								$new[$v]='';
							}
						}
						$add=$new;
						$this->arr[]=$add;
					}elseif(count($add)==count($this->arr[0])){
						$this->arr[]=$add;
					}
				}
			}
		}
		return $this;
	}

	public function delete($id=null){
		// if($id){
		// 	$count=count($this->arr);
		// 	$id=$count>$id?(int)($id):$count;
		// 	$stor=$this->arr;
		// 	$this->arr=[];
		// 	$this->arr[]=$stor[$id-1];
		// }
		if(is_int($id)){
			$count=count($this->arr);
			$id=$count>$id?(int)($id):$count;
			$stor=$this->arr;
			$this->arr=[];
			$this->arr[]=$stor[$id-1];
			unset($stor);
		}elseif(is_string($id)||is_array($id)){
			if(is_string($id)){
				$id=explode(',',$id);
			}
			$count=count($this->arr);
			$stor=$this->arr;
			$this->arr=[];
			foreach ($id as $v) {
				if($count<$v){
					continue;
				}
				$this->arr[]=$stor[(int)($v)-1];
			}
			unset($stor);
		}
		if($this->method){
			$this->error['delete']='在调用delete方法前不能调用field方法';
			return $this->getError();
		}else{
			$stor=$this->old;
			foreach ($this->old as $k => $v) {
				foreach ($this->arr as $k1 => $v1) {
					if($v==$v1){
						unset($this->old[$k]);
					}
				}
			}
			$this->arr=array_values($this->old);
			$this->old=$stor;
			return $this->arr;
		}
	}

	public function uniq($field=''){
		if(!$field){
			$stor=[];
			foreach ($this->arr as  $v) {
				if(!in_array($v,$stor)){
					$stor[]=$v;
				}
			}
			$this->arr=$stor;
			return $this;
		}else{
			if(is_string($field)){
				$field=explode(',',$field);
			}
			$fields1=[];
			foreach ($this->arr as $v) {
				$fields=[];
				foreach ($field as $v1) {
					$fields[]=$v[$v1];
				}
				$fields1[]=$fields;
			}
			$stor=[];
			$cut=[];
			foreach ($fields1 as $k => $v) {
				if(!in_array($v,$stor)){
					$stor[]=$v;
				}else{
					$cut[]=$k;
				}
			}
			foreach ($cut as $v) {
				unset($this->arr[$v]);
			}
			$this->arr=array_values($this->arr);
			return $this;
		}
	}

	public function sortOut($arr=''){
		$do=$arr?$arr:$this->arr;
		foreach ($do as $v) {
			$count=count($v);
			break;
		}
		$all=[];
		for($i=0;$i<$count;$i++){
			$input=[];
			foreach ($do as $k => $v) {
				$input[$k]=$v[$i];
			}
			$all[]=$input;
		}
		if($arr){
			return $all;
		}else{
			$this->arr=$all;
			return $this;
		}
	}

	public function limit($limit){
		$stor=[];
		if(is_int($limit)||(is_string($limit)&&!preg_match('/,/',$limit))){
			if(is_string($limit)){
				$limit=(int)($limit);
			}
			$i=0;
			foreach ($this->arr as $v) {
				if($i==$limit){
					break;
				}
				$stor[]=$v;
				$i++;
			}
			$this->arr=$stor;
			return $this;
		}elseif(is_string($limit)&&preg_match('/(.*),(.*)/',$limit,$newlimit)){
			foreach ($this->arr as $k => $v) {
				if(count($stor)>=$newlimit[2]){
					break;
				}
				if($k+1>=$newlimit[1]){
					$stor[]=$v;
				}
			}
			$this->arr=$stor;
			return $this;
		}
	}

	public function count(){
		return count($this->arr);
	}

	public function getError(){
		return $this->error;
	}

	private function orall($str){
		static $where=[];
		static $flag=true;
		if(preg_match_all('/(.*) or (.*)/i',$str,$arr)){
			$or=true;
			if($flag){
				$where[]=substr($str,0,stripos($str,'or')-1);
				$flag=false;
			}
			$where[]=$arr[2][0];
			if(preg_match_all('/(.*) or (.*)/i',$arr[1][0],$arr1)){
				$this->orall($arr[1][0]);
			}
		}
		return $where;
	}

	private function andall($str){
		static $flag=true;
		if(preg_match_all('/(.*) and (.*)/i',$str,$arr)){
			if($flag){
				$this->whereand[]=substr($str,0,stripos($str,'and')-1);
				$flag=false;
			}
			if(!preg_match_all('/(.*) and (.*)/i',$arr[2][0],$arr1)){
				$this->whereand[]=$arr[2][0];
			}
			if(preg_match_all('/(.*) and (.*)/i',$arr[1][0],$arr1)){
				$this->andall($arr[1][0]);
			}
		}
		return $this->whereand;
	}

	public function setKeys($keys,$newkeys=''){
		$this->return=[];
		if(is_string($keys)&&is_string($newkeys)){
			$stor=[];
			$stor[$keys]=$newkeys;
			$keys=$stor;
		}
		if(is_array($keys)){
			foreach ($this->arr as $v) {
				$stor=[];
				foreach ($v as $k1 => $v1) {
					if(array_key_exists($k1, $keys)){
						$stor[$keys[$k1]]=$v1;
					}else{
						$stor[$k1]=$v1;
					}
				}
				$this->return[]=$stor;
			}
			$this->arr=$this->return;
			$this->return=[];
			return $this;
		}
	}

	public function end(){
		$this->arr=$this->old;
		return $this;
	}
}
?>