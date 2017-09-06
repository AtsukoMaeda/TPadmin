<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title><?php echo ($webinfo["webtitle"]); ?>-后台管理</title>
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <link rel="stylesheet" type="text/css" href="/tp/Public/back/lib/bootstrap/css/bootstrap.min.css">
    
    <link rel="stylesheet" type="text/css" href="/tp/Public/back/stylesheets/theme.css">
    <link rel="stylesheet" href="/tp/Public/back/lib/font-awesome/css/font-awesome.css">

    <script src="/tp/Public/back/lib/jquery-1.7.2.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="/tp/Public/back/js/layer/layer.js"></script>
   

    <!-- Demo page code -->

    <style type="text/css">
    .tishi{
        color: red;
    }
        .navbar .nav > li > a:focus{
            color: #fff;
        }
        #line-chart {
            height:300px;
            width:800px;
            margin: 0px auto;
            margin-top: 1em;
        }
        .brand { font-family: georgia, serif; }
        .brand .first {
            color: #ccc;
            font-style: italic;
        }
        .brand .second {
            color: #fff;
            font-weight: bold;
        }
    </style>

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="<?php echo C('FILE_PATH'); echo ($webinfo["logo"]); ?>">
  <!--   <link rel="apple-touch-icon-precomposed" sizes="144x144" href="../assets/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="../assets/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="../assets/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="../assets/ico/apple-touch-icon-57-precomposed.png"> -->
  </head>

  <!--[if lt IE 7 ]> <body class="ie ie6"> <![endif]-->
  <!--[if IE 7 ]> <body class="ie ie7 "> <![endif]-->
  <!--[if IE 8 ]> <body class="ie ie8 "> <![endif]-->
  <!--[if IE 9 ]> <body class="ie ie9 "> <![endif]-->
  <!--[if (gt IE 9)|!(IE)]><!--> 
  <body class=""> 
  <!--<![endif]-->
    
    <div class="navbar">
        <div class="navbar-inner">
                <ul class="nav pull-right">
                    
                    <!-- <li><a href="#" class="hidden-phone visible-tablet visible-desktop" role="button">Settings</a></li> -->
                    <li id="fat-menu" class="dropdown">
                        <a href="javascript:void(0)" role="button" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="icon-user"></i> <?php echo (session('admin')); ?>
                            <i class="icon-caret-down"></i>
                        </a>

                        <ul class="dropdown-menu">
                            
                            <li><a tabindex="-1" href="<?php echo U('Login/logout');?>">退出</a></li>
                            <li class="divider"></li>
                            <li><a tabindex="-1" href="#updatePwd" role="button" data-toggle="modal" class="toPwd">修改密码</a></li>
                        </ul>
                    </li>
                    
                </ul>
                <a class="brand" href="<?php echo U('Index/index');?>"><i class="icon-th-large"></i><span class="second">&nbsp;&nbsp;<?php echo ($webinfo["webtitle"]); ?></span></a>
        </div>
    </div>
<div class="sidebar-nav">
        <a href="<?php echo U('Home/Index/index');?>" class="nav-header" target="_blank"><i class="icon-home"></i>网站首页</a>
        <?php if(is_array($left)): $i = 0; $__LIST__ = $left;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i; $icon=explode('|',$key); ?>
        <a href="#<?php echo ($icon["0"]); ?>" class="nav-header <?php if(CONTROLLER_NAME!= $v[0]['c']){ ?>collapsed<?php } ?>" data-toggle="collapse"><i class="<?php echo ($icon["1"]); ?>"></i><?php echo ($icon["0"]); ?><i class="icon-chevron-up"></i></a>
            <ul id="<?php echo ($icon["0"]); ?>" class="nav nav-list collapse <?php if(CONTROLLER_NAME==$v[0]['c']){ ?>in<?php } ?>">
        <?php foreach($v as $k1=>$v1){ ?>
                <li ><a href='<?php echo U("$v1[m]/$v1[c]/$v1[action]");?>'><?php echo ($v1["menuname"]); ?></a></li>
        <?php } ?>
            </ul><?php endforeach; endif; else: echo "" ;endif; ?>
        <?php if(C('OPEN_UP')){ ?>
        <a href="#open-up" class="nav-header <?php if(CONTROLLER_NAME!='Menu'){ ?>collapsed<?php } ?>" data-toggle="collapse"><i class="icon-briefcase"></i>开发者专用 <i class="icon-chevron-up"></i></a>
        <ul id="open-up" class="nav nav-list collapse <?php if(CONTROLLER_NAME=='Menu'){ ?>in<?php } ?>">
            <li ><a href="<?php echo U('Menu/menuAdd');?>">添加菜单</a></li>
            <li ><a href="<?php echo U('Menu/menuList');?>">菜单管理</a></li>
        </ul>
        <?php } ?>
    </div>

<div class="content">
        
        <div class="header">
            <h1 class="page-title">首页</h1>
        </div>
        
                <ul class="breadcrumb">
            <li><a href="javascript:void(0)">首页</a> <span class="divider">/</span></li>
        </ul>

        <div class="container-fluid">
            <div class="row-fluid">
                    
<?php if(cookie('loginInfo')['login_ip']!=$nowInfo['login_ip']){ ?>
<div class="row-fluid">
    <div class="alert alert-info">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>提醒:</strong> 您此次登陆的IP与上次的不同!
    </div>
</div>
<?php } ?>
<div class="row-fluid">
    <div class="block span6">
        <a href="#tablewidget" class="block-heading" data-toggle="collapse">用户</a>
        <div id="tablewidget" class="block-body collapse in">
            <table class="table">
              <tbody>
                <tr>
                  <td>当前用户</td>
                  <td><?php echo (session('admin')); ?></td>
                </tr>
                <tr>
                  <td>级别</td>
                  <td><?php echo ($nowInfo["role"]); ?></td>
                </tr>
                <tr>
                  <td>本次登陆地址</td>
                  <td><i class="icon-map-marker light-orange bigger-110"></i>&nbsp;<?php echo ($nowInfo["login_address"]); ?></td>
                </tr>
                <tr>
                  <td>上次登陆地址</td>
                  <td><i class="icon-map-marker light-orange bigger-110"></i>&nbsp;<?php echo cookie('loginInfo')['login_address'];?></td>
                </tr>
                <tr>
                  <td>本次登陆IP</td>
                  <td><i class="icon-map-marker light-orange bigger-110"></i>&nbsp;<?php echo ($nowInfo["login_ip"]); ?></td>
                </tr>
                <tr>
                  <td>上次登陆IP</td>
                  <td><i class="icon-map-marker light-orange bigger-110"></i>&nbsp;<?php echo cookie('loginInfo')['login_ip'];?></td>
                </tr>
                <tr>
                  <td>上次登陆时间</td>
                  <td><?php echo date('Y-m-d H:i:s',cookie('loginInfo')['login_time']);?></td>
                </tr>
                <tr>
                  <td>本次登陆时间</td>
                  <td><?php echo date('Y-m-d H:i:s',$nowInfo['login_time']);?></td>
                </tr>
              </tbody>
            </table>
        </div>
    </div>
    <div class="block span6">
        <a href="#widget1container" class="block-heading" data-toggle="collapse">版本 </a>
       <div id="tablewidget" class="block-body collapse in">
            <table class="table">
              <tbody>
                <tr>
                  <td>操作系统</td>
                  <td><?php echo php_uname('s').'&nbsp;'.php_uname('r');?></td>
                </tr>
                <tr>
                  <td>服务器版本</td>
                  <td><?php echo ($_SERVER['SERVER_SOFTWARE']); ?></td>
                </tr>
                <tr>
                  <td>当前PHP版本</td>
                  <td><?php echo PHP_VERSION;?></td>
                </tr>
                <tr>
                  <td>Zend版本</td>
                  <td><?php echo Zend_Version();?></td>
                </tr>
                <tr>
                  <td>服务器IP</td>
                  <td><i class="icon-map-marker light-orange bigger-110"></i>&nbsp;<?php echo GetHostByName($_SERVER['SERVER_NAME']);?></td>
                </tr>
              </tbody>
            </table>
        </div>
    </div>
</div>
<footer>
    <hr>
    <!-- Purchase a site license to remove this link from the footer: http://www.portnine.com/bootstrap-themes -->
    <p class="pull-right">A <a href="http://www.portnine.com/bootstrap-themes" target="_blank">Free Bootstrap Theme</a> by <a href="http://www.portnine.com" target="_blank">Portnine</a></p>


    <p>&copy; 2012 <a href="http://www.portnine.com" target="_blank">Portnine</a></p>
</footer>
                    
        </div>
    </div>
</div>
<div class="modal small hide fade" id="updatePwd" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">修改密码</h3>
    </div>
    <div class="modal-body">
    <form id="updatePwdFrom">
      <label>原密码</label>
      <input type="password" name="pwd"><d class="tishi pwd"></d>
      <label>新密码</label>
      <input type="password" name="npwd"><d class="tishi npwd"></d>
      <label>确认新密码</label>
      <input type="password" name="npwd2"><d class="tishi npwd2"></d>
      </form>
    </div>
    <div class="modal-footer">
        <button class="btn xiaoshi" data-dismiss="modal" aria-hidden="true">取消</button>
        <button class="btn btn-danger updatePwd">确定</button>
    </div>
</div>


    <script src="/tp/Public/back/lib/bootstrap/js/bootstrap.min.js"></script>
    <script>
    $('.toPwd').click(function() {
        $('.tishi').hide();
        $('#updatePwdFrom :input').val('');
    });
    $('.updatePwd').click(function() {
            var data=$('#updatePwdFrom').serialize(),
            that=this;
            $.post("<?php echo U('Admin/pwdUpdate');?>",data,function(back){
                $('.tishi').hide();
                if(back=='1'){
                    layer.msg('修改密码成功！');
                    $('.xiaoshi').click();
                    return 0;
                }else if(back=='2'){
                    layer.msg('修改密码失败！');
                    return 0;
                }
                for(k in back) {
                    $('.'+k).text('*'+back[k]).show();
                }
            },'json');
        });
        $('#updatePwdFrom').keydown(function(event) {
            if(event.keyCode==13){
                $('.updatePwd').click();
            }
        });  
        $("[rel=tooltip]").tooltip();
        $(function() {
            $('.demo-cancel-click').click(function(){return false;});
        });
    </script>
  </body>
</html>