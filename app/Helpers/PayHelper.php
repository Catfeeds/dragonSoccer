<?php
namespace App\Helpers;
use Config;
use App\Helpers\CurlHelper;
class PayHelper{

	//接口签名
    public static function aligetSign(array $toBeSigned, $verify = false){
        ksort($toBeSigned);
        $stringToBeSigned = '';
        foreach ($toBeSigned as $k => $v) {
            if ($verify && $k != 'sign' && $k != 'sign_type') {
                $stringToBeSigned .= $k.'='.$v.'&';
            }
            if (!$verify && $v !== '' && !is_null($v) && $k != 'sign' && '@' != substr($v, 0, 1)) {
                $stringToBeSigned .= $k.'='.$v.'&';
            }
        }
        $stringToBeSigned = substr($stringToBeSigned, 0, -1);
        unset($k, $v);

        return $stringToBeSigned;
    }
   
    public static function alipay($data = array()){
    	$gatewayUrl= 'https://openapi.alipay.com/gateway.do';

		$pay['app_id'] = Config('pay.alipay.app_id');
		$pay['method'] = 'alipay.fund.trans.toaccount.transfer';
		$pay['format'] ='json';
		$pay['charset'] ='utf-8';
		$pay['sign_type'] = 'RSA2';
		$pay['version'] = '1.0';
		$pay['timestamp'] = date('Y-m-d H:i:s');
		//$pay['app_auth_token'] = '';

		$data['out_biz_no'] = $data['sn'];;
		$data['payee_type'] = 'ALIPAY_USERID';
		$data['payee_account'] = $data['payuser'];
		$data['amount'] = $data['amount'];
		$data['payer_show_name'] = '';
		$data['payee_real_name'] = '';
		$data['remark'] = $data['remark'];

		$apipay['biz_content'] = json_encode($data);

		$paystr = self::aligetSign(array_merge($apipay, $pay));
        $res = "-----BEGIN RSA PRIVATE KEY-----\n".wordwrap(Config::get('pay.alipay.private_key'), 64, "\n", true)."\n-----END RSA PRIVATE KEY-----";
        openssl_sign($paystr, $sign, $res, OPENSSL_ALGO_SHA256);
        $sign = base64_encode($sign);

        $pay['sign'] = $sign;
        //系统参数放入GET请求串
        $requestUrl = $gatewayUrl . "?";
        foreach ($pay as $k => $v) {
            $requestUrl .= "$k=" . urlencode($v) . "&";
        }
        $requestUrl = substr($requestUrl, 0, -1);

         $apipaystr='';
        foreach ($apipay as $k => $v) {
            $apipaystr .= "$k=" . urlencode($v) . "&";
        }
        $apipaystr = substr($apipaystr, 0, -1);
        $requestStr = CurlHelper::curl_ali($requestUrl,$apipaystr);

		return json_decode($requestStr,true);
    }
	

	public static function wechatgetSign(array $data){
        ksort($data);
        $buff = '';
        foreach ($data as $k => $v) {
            $buff .= ($k != 'sign' && $v != '' && !is_array($v)) ? $k.'='.$v.'&' : '';
        }

        $str = md5(trim($buff, '&').'&key='.Config::get('pay.wechat.key'));

        return $str;
    }

    public static function toXml($data){
        if (!is_array($data) || count($data) <= 0) {
            throw new InvalidArgumentException('convert to xml error!invalid array!');
        }

        $xml = '<xml>';
        foreach ($data as $key => $val) {
            $xml .= is_numeric($val) ? '<'.$key.'>'.$val.'</'.$key.'>' :'<'.$key.'><![CDATA['.$val.']]></'.$key.'>';
        }
        $xml .= '</xml>';

        return $xml;
    }


    public static function wecahtpay($data = array()){
    	$gatewayUrl= 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';

		$pay['mch_appid'] = Config::get('pay.wechat.appid');
		$pay['mchid'] = Config::get('pay.wechat.mch_id');
		//$pay['device_info'] = '';
		$pay['nonce_str'] = md5(time().rand(1000,9999));
		$pay['partner_trade_no'] = $data['sn'];
		$pay['openid'] = $data['payuser']; 
		$pay['check_name'] =  'NO_CHECK';
		$pay['re_user_name'] = '';
		$pay['amount'] =  $data['amount']*100;//分
		$pay['desc'] = $data['remark'];
		$pay['spbill_create_ip'] =  $_SERVER['REMOTE_ADDR'];

		$pay['sign'] = self::wechatgetSign($pay);

        $requestStr = CurlHelper::curl_wechat($gatewayUrl,self::toXml($pay));

        $params = (array)simplexml_load_string($requestStr, 'SimpleXMLElement', LIBXML_NOCDATA);

		return $params;
    }

}


