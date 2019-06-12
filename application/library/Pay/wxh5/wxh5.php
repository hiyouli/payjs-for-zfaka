<?php
/**
 * File: wxh5.php
 * Functionality: 微信h5支付
 * Author: 资料空白
 * Date: 2018-09-05
 */
namespace Pay\wxh5;

use \Payment\Client\Charge;
use \Payment\Common\PayException;
use \Payment\Client\Notify;
use \Payment\Config;

use \Pay\wxh5\callback;

class wxh5
{
	private $paymethod ="wxh5";
	//处理请求
	public function pay($payconfig,$params)
	{
		$config = [
			'use_sandbox' => false,	
			'mch_id' => $payconfig['configure3'],
			'md5_key' => $payconfig['app_secret'],
			'sign_type' => $payconfig['sign_type'],
			'fee_type'  => 'CNY',
			'redirect_url' => $params['weburl']. "/query/auto/{$params['orderid']}.html",
			'notify_url' => $params['weburl'] . "/notify/{$this->paymethod}.html",
			'return_raw' => true
		];

       // 构造订单基础信息
$data = [
    'body' => '订单测试',                         // 订单标题
    'total_fee' => 2,                            // 订单金额
    'out_trade_no' => time(),                    // 订单号
    'attach' => 'test_order_attach',             // 订单附加信息(可选参数)
    'notify_url' => 'http://fk.bouhr.com/product/notify/?paymethod=wxh5',     // 异步通知地址(可选参数)
];

$result = $payjs->jsapi($data);
print_r($result);
      
      
      
		try {
			$qr = Charge::run(Config::WX_CHANNEL_WAP, $config, $data);
			if(is_array($qr) AND !empty($qr)){
				if(isset($qr['return_code']) AND $qr['return_code']=="SUCCESS"){
					if($qr['result_code']=="SUCCESS"){
						$result_params = array('type'=>0,'subjump'=>0,'paymethod'=>$this->paymethod,'qr'=>$params['qrserver'].$qr['code_url'],'payname'=>$payconfig['payname'],'overtime'=>$payconfig['overtime'],'money'=>$params['money']);
						return array('code'=>1,'msg'=>'success','data'=>$result_params);
					}else{
						return array('code'=>1002,'msg'=>$qr['err_code_des'],'data'=>'');
					}
				}else{
					return array('code'=>1002,'msg'=>$qr['return_msg'],'data'=>'');
				}
			}else{
				return array('code'=>1002,'msg'=>'支付接口请求失败','data'=>'');
			}
		} catch (PayException $e) {
			return array('code'=>1001,'msg'=>$e->errorMessage(),'data'=>'');
		} catch (\Exception $e) {
			return array('code'=>1000,'msg'=>$e->getMessage(),'data'=>'');
		}
	}
	
	public function notify(array $payconfig)
	{
		try {
			file_put_contents(YEWU_FILE, CUR_DATETIME.'-params:'.json_encode($_POST).PHP_EOL, FILE_APPEND);
			$config = [
				'use_sandbox' => false,
				'app_id' => $payconfig['app_id'],
				'mch_id' => $payconfig['configure3'],
				'md5_key' => $payconfig['app_secret'],
				'sign_type' => $payconfig['sign_type'],
				'app_cert_pem' => LIB_PATH.'Pay/'.$this->paymethod.'/pem/weixin_app_cert.pem',
				'app_key_pem' => LIB_PATH.'Pay/'.$this->paymethod.'/pem/weixin_app_key.pem',
				'fee_type'  => 'CNY',
				'notify_url' => $params['weburl'] . "/notify/{$this->paymethod}.html",
				'return_raw' => true
			];
			$callback = new \Pay\wxh5\callback();
			return $ret = Notify::run("wx_charge", $config,$callback);// 处理回调，内部进行了签名检查	
		} catch (\Exception $e) {
			return 'error|Exception:'.$e->getMessage();
		}
	}
	
}