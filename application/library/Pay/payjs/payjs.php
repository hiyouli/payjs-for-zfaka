<?php
/**
 * File: payjs.php
 * Functionality: payjs
 * Author: 资料空白
 * Date: 2018-6-8
 */

namespace Pay\payjs;

use \Pay\notify;

class payjs
{
    private $paymethod = "payjs";

    private $payjs_native_url = 'https://payjs.cn/api/native';

    //处理请求
    public function pay($payconfig, $params)
    {
        clearstatcache();
        $data         = [
            'mchid'        => $payconfig['app_id'],
            'body'         => $params['orderid'],
            'out_trade_no' => $params['orderid'],
            'total_fee'    => $params['money'] * 100,
            "notify_url"   => $params['weburl'] . '/product/notify/?paymethod='.$this->paymethod,
        ];
        $this->key    = $payconfig['app_secret'];
        $data['sign'] = $this->sign($data);

        $result = $this->post($data, $this->payjs_native_url);
        $result = json_decode($result, true);

        $result_params = [
            'type'      => 0,
            'subjump'   => 0,
            'paymethod' => $this->paymethod,
            'qr'        => $params['qrserver'] . urlencode($result['code_url']),
            'payname'   => $payconfig['payname'],
            'overtime'  => $payconfig['overtime'],
            'money'     => $params['money'],
        ];

        return ['code' => 1, 'msg' => 'success', 'data' => $result_params];
    }

    public function notify()
    {
        $data = $_POST;

        if ($data['return_code'] == 1) {
            $config = [
                'paymethod' => $this->paymethod,
                'tradeid'   => $data['payjs_order_id'],
                'paymoney'  => $data['total_fee'] / 100,
                'orderid'   => $data['out_trade_no'],
            ];
            $notify = new \Pay\notify();
            $notify->run($config);
        }

        return 'success';
    }

    public function post($data, $url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $rst = curl_exec($ch);
        curl_close($ch);
        return $rst;
    }

    public function sign(array $attributes)
    {
        ksort($attributes);
        $sign = strtoupper(md5(urldecode(http_build_query($attributes)) . '&key=' . $this->key));
        return $sign;
    }

}