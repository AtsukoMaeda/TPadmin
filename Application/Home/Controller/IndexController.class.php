<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
    	$h=M('admin');
        $this->show('<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} body{ background: #fff; font-family: "微软雅黑"; color: #333;font-size:24px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.8em; font-size: 36px } a,a:hover{color:blue;}</style><div style="padding: 24px 48px;"> <h1>:)</h1><p>欢迎使用 <b>ThinkPHP</b>！</p><br/>版本 V{$Think.version}</div><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_55e75dfae343f5a1"></thinkad><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script>','utf-8');
    	$arr=[
	['term'=>'akb','name'=>'前田敦子','age'=>26,'mouth'=>7],
	['term'=>'akb','name'=>'渡边麻友','age'=>24,'mouth'=>4],
	['term'=>'akb','name'=>'大岛优子','age'=>28,'mouth'=>10],
	['term'=>'akb','name'=>'柏木由纪','age'=>26,'mouth'=>6],
	['term'=>'akb','name'=>'小岛阳菜','age'=>28,'mouth'=>3],
	['term'=>'akb','name'=>'板野友美','age'=>26,'mouth'=>7],
	['term'=>'akb','name'=>'高桥南','age'=>26,'mouth'=>4],
	['term'=>'akb','name'=>'筱田麻里子','age'=>30,'mouth'=>3],
	['term'=>'ske','name'=>'松井珠理奈','age'=>19,'mouth'=>3],
	['term'=>'ske','name'=>'松井玲奈','age'=>26,'mouth'=>8],
	['term'=>'nmb','name'=>'山本彩','age'=>24,'mouth'=>11],
	['term'=>'nmb','name'=>'山本彩','age'=>24,'mouth'=>11],
	];
	$a=new \Think\AtsukoArr($arr);
	$data=$a->where('age IN(26,24)')->order('age DESC')->uniq()->select();
	dump($data);
    }
}