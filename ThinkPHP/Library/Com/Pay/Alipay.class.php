<?php
namespace Com\Pay;

class Alipay
{

    private $config;

    public function __construct($config = null)
    {
        if($config){
            $this->config = unserialize($config);
        }else{
            $this->config = array(
                'pid'=>'2088421215797145',
                'key'=>'a6ox2om7z2ywnjtz0zqd6jupd7ghn73l',
                'seller_email'=>'zhoushengxun@jaworld.com.cn',
                'notify_url'=>(C('YUMING').U('Home/Login/pay_respone')),
                'return_url'=>(C('YUMING').U('Home/User/my_order')),
            );
        }
        //这里我们通过TP的C函数把配置项参数读出，赋给$alipay_config；
        $this->config['alipay_config'] = array(
           'partner' =>$this->config['pid'],   //这里是你在成功申请支付宝接口后获取到的PID；
            'key'=>$this->config['key'],//这里是你在成功申请支付宝接口后获取到的Key
            'sign_type'=>strtoupper('MD5'),
            'input_charset'=> strtolower('utf-8'),
            // 'cacert'=> getcwd().'\\domain.crt',
            'cacert'=> getcwd().'\\cacert.pem',
            'transport'=> 'http',
        );

        /**
         * 引入支付宝
         */
        vendor('Alipay.Corefunction');
        vendor('Alipay.Md5function');
        vendor('Alipay.Notify');
        vendor('Alipay.Submit');

    }

/*生成支付按钮*/
    public function getCode($order_info)
    {
        /*********************************************************
        把alipayapi.php中复制过来的如下两段代码去掉，
        第一段是引入配置项，
        第二段是引入submit.class.php这个类。
       为什么要去掉？？
        第一，配置项的内容已经在项目的Config.php文件中进行了配置，我们只需用C函数进行调用即可；
        第二，这里调用的submit.class.php类库我们已经在PayAction的_initialize()中已经引入；所以这里不再需要；
              *****************************************************/

          /**************************请求参数**************************/
          $payment_type = "1"; //支付类型 //必填，不能修改
          $notify_url = $this->config['notify_url']; //服务器异步通知页面路径
          $return_url = $this->config['return_url']; //页面跳转同步通知页面路径
          $seller_email = $this->config['seller_email'];//卖家支付宝帐户必填
          $out_trade_no = $order_info['order_sn'];//商户订单号 通过支付页面的表单进行传递，注意要唯一！
          $subject = '家安采购订单';  //订单名称 //必填 通过支付页面的表单进行传递
          $total_fee = $order_info['pay_money'];   //付款金额  //必填 通过支付页面的表单进行传递
          //$total_fee = 0.01;   //付款金额  //必填 通过支付页面的表单进行传递
          $body = '采购上家安';  //订单描述 通过支付页面的表单进行传递
          $show_url = '';  //商品展示地址 通过支付页面的表单进行传递
          $anti_phishing_key = "";//防钓鱼时间戳 //若要使用请调用类文件submit中的query_timestamp函数
          $exter_invoke_ip = get_client_ip(); //客户端的IP地址

          //商品数量
          $quantity = "1";
          //必填，建议默认为1，不改变值，把一次交易看成是一次下订单而非购买一件商品
          //物流费用
          $logistics_fee = "0.00";
          //必填，即运费
          //物流类型
          $logistics_type = "EXPRESS";
          //必填，三个值可选：EXPRESS（快递）、POST（平邮）、EMS（EMS）
          //物流支付方式
          $logistics_payment = "SELLER_PAY";
          //必填，两个值可选：SELLER_PAY（卖家承担运费）、BUYER_PAY（买家承担运费）
          //订单描述
          //收货人姓名
          $receive_name = $order_info['consignee'];
          //如：张三
          //收货人地址
          $receive_address = 'XX省XXX市XXX区XXX路XXX小区XXX栋XXX单元XXX号';
          //如：XX省XXX市XXX区XXX路XXX小区XXX栋XXX单元XXX号
          //收货人邮编
          $receive_zip = '123456';
          //如：123456

          //收货人电话号码
          $receive_phone = '0000-0000000';
          //如：0571-88158090

          //收货人手机号码
          $receive_mobile = '13232139621';
          //如：13312341234


          /************************************************************/

      //构造要请求的参数数组，无需改动
      $parameter = array(
          "service" => "create_direct_pay_by_user",
          "partner" => trim($this->config['pid']),
          "payment_type"  => $payment_type,
          "notify_url"  => $notify_url,
          "return_url"  => $return_url,
          "seller_email"  => $seller_email,
          "out_trade_no"  => $out_trade_no,
          "subject" => $subject,
          "price" => $total_fee,
          "quantity"  => $quantity,
          "logistics_fee" => $logistics_fee,
          "logistics_type"  => $logistics_type,
          "logistics_payment" => $logistics_payment,
          "body"  => $body,
          "show_url"  => $show_url,
          "receive_name"  => $receive_name,
          "receive_address" => $receive_address,
          "receive_zip" => $receive_zip,
          "receive_phone" => $receive_phone,
          "receive_mobile"  => $receive_mobile,
          "_input_charset"  => trim(strtolower($this->config['alipay_config']['input_charset']))
      );
    //   var_dump($parameter);die;

    //   var_dump($parameter);die;


        //建立请求
        $alipaySubmit = new \AlipaySubmit($this->config['alipay_config']);
        $html_text = $alipaySubmit->buildRequestForm($parameter,"post", "");
        echo $html_text;die;
    }

    public function respone()
    {
        // file_put_contents('./pay.txt',var_export($_REQUEST,true));
        /*
        同理去掉以下两句代码；
        */
        //require_once("alipay.config.php");
        //require_once("lib/alipay_notify.class.php");
        //计算得出通知验证结果
        $alipayNotify = new \AlipayNotify($this->config['alipay_config']);
        $verify_result = $alipayNotify->verifyNotify();
        // if(!$verify_result){
        //     file_put_contents('./pay.txt','验证失败',FILE_APPEND);
        // }else{
        //     file_put_contents('./pay.txt','验证成功！',FILE_APPEND);
        // }

        if($verify_result) {
            // file_put_contents('./pay.txt','进来了',FILE_APPEND);
       //验证成功
           //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
           $out_trade_no   = $_POST['out_trade_no'];      //商户订单号
           $trade_no       = $_POST['trade_no'];          //支付宝交易号
           $trade_status   = $_POST['trade_status'];      //交易状态
           $total_fee      = $_POST['total_fee'];         //交易金额
           $notify_id      = $_POST['notify_id'];         //通知校验ID。
           $notify_time    = $_POST['notify_time'];       //通知的发送时间。格式为yyyy-MM-dd HH:mm:ss。
           $buyer_email    = $_POST['buyer_email'];       //买家支付宝帐号；
           $parameter = array(
             "out_trade_no"     => $out_trade_no, //商户订单编号；
             "trade_no"     => $trade_no,     //支付宝交易号；
             "total_fee"     => $total_fee,    //交易金额；
             "trade_status"     => $trade_status, //交易状态
             "notify_id"     => $notify_id,    //通知校验ID。
             "notify_time"   => $notify_time,  //通知的发送时间。
             "buyer_email"   => $buyer_email,  //买家支付宝帐号；
           );
           if($_POST['trade_status'] == 'TRADE_FINISHED') {

           }else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {

               if(!check_order_status($out_trade_no)){
                //    file_put_contents('./pay.txt','1进来了',FILE_APPEND);
                   $data = array(
                       'order_sn'=>$out_trade_no,
                       'des'=>('订单交易：'.$out_trade_no),
                       'money'=>$total_fee,
                   );
                   orderhandle($data);
                   //进行订单处理，并传送从支付宝返回的参数；
               }
            }
            echo "success";        //请不要修改或删除
        }else {
            //验证失败
            echo "fail";
            // file_put_contents('./pay.txt',var_export($verify_result,true),FILE_APPEND);
            // file_put_contents('./pay.txt',var_export($this->config,true),FILE_APPEND);
            file_put_contents('./pay.txt','验证失败',FILE_APPEND);
        }
    }

}
?>
