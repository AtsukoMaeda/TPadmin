<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Bootstrap Admin</title>
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <link rel="stylesheet" type="text/css" href="/tp/Public/back/lib/bootstrap/css/bootstrap.css">
    
    <link rel="stylesheet" type="text/css" href="/tp/Public/back/stylesheets/theme.css">
    <link rel="stylesheet" href="/tp/Public/back/lib/font-awesome/css/font-awesome.css">

    <script src="/tp/Public/back/lib/jquery-1.7.2.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="/tp/Public/back/js/layer/layer.js"></script>

    <!-- Demo page code -->

    <style type="text/css">
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
    <link rel="shortcut icon" href="../assets/ico/favicon.ico">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="../assets/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="../assets/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="../assets/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="../assets/ico/apple-touch-icon-57-precomposed.png">
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
                    
                </ul>
                <a class="brand" href="index.html"><span class="first">Your</span> <span class="second">Company</span></a>
        </div>
    </div>
    


    

    
        <div class="row-fluid">
    <div class="dialog">
        <div class="block">
            <p class="block-heading">后台登陆</p>
            <div class="block-body">
                <form>
                    <label>账号</label>
                    <input type="text" class="span12" id="username">
                    <label>密码</label>
                    <input type="password" class="span12" id="password">
                    <label>验证码</label>
                    <input type="text" class="span6" id="checkword">
                    <img src="<?php echo U('Admin/Login/verify');?>" class="span6 pull-right" style="height:30px;" onclick="this.src='<?php echo U('Admin/Login/verify','','');?>/'+Math.random()">
                    <a href="javascript:void(0)" class="btn btn-primary pull-right" id="login">登陆</a>
                    <label class="remember-me"><input type="checkbox" id="online">记住登录</label>
                    <div class="clearfix"></div>
                </form>
            </div>
        </div>
<!--         <p class="pull-right" style=""><a href="http://www.portnine.com" target="blank">Theme by Portnine</a></p>
        <p><a href="reset-password.html">Forgot your password?</a></p> -->
    </div>
</div>


    


    <script src="/tp/Public/back/lib/bootstrap/js/bootstrap.js"></script>
    <script type="text/javascript">
        $("[rel=tooltip]").tooltip();
        $(function() {
            $('.demo-cancel-click').click(function(){return false;});
        });
    </script>
    
  </body>
</html>
<script>
$('#login').click(function(){
    var username=$('#username').val(),
        password=$('#password').val(),
        checkword=$('#checkword').val(),
        online=$('#online').prop('checked');
        $.ajax({
            url:'/tp/Admin/Login/index.html',
            data:{'username':username,'password':password,'checkword':checkword,'online':online},
            dataType:'json',
            type:'post',
            success:function(back){
                console.log(back);
                switch(back){
                    case 1:
                        layer.msg('账号密码错误！');
                        break;
                    case 2:
                        layer.msg('登陆成功！');
                        window.location.href='<?php echo U('Admin/Index/index');?>';
                        break;
                    case 3:
                        layer.msg('验证码错误！');
                        break;
                }
            } 
        });
});
$(document).keydown(function(event){
    if(event.keyCode==13){
        $('#login').trigger('click');
    }
});
</script>