<?php
namespace App\Helpers;
use OSS\OssClient;
use OSS\Core\OssException;

class OssUploadHelper{
	private static  $accessKeyId = "LTAIxhS7vQrxRUoO";
	private static  $accessKeySecret = "TgFP0UA077fjxiU4RHmvtTiNaxyVKX";
	private static  $endpoint = "http://oss-cn-beijing.aliyuncs.com";

	private static function gmt_iso8601($time) {
        $dtStr = date("c", $time);
        $mydatetime = new \DateTime($dtStr);
        $expiration = $mydatetime->format(\DateTime::ISO8601);
        $pos = strpos($expiration, '+');
        $expiration = substr($expiration, 0, $pos);
        return $expiration."Z";
    }

    /* 
    * $bucket 
    * $dir 表示用户上传的数据,必须是以$dir开始 
    * $expire 设置该policy超时时间
    * 最大文件大小,默认 1GB=1048576000
    */
    public static function makeConfig($bucket,$dir = 'default/',$expire = 30 ,$maxsize = 1048576000){
	    $host = 'http://'.$bucket.'.'.substr(self::$endpoint,7);
	    $end = time()+$expire; 
	    $arr = array(
			'expiration' => self::gmt_iso8601($end),
			'conditions'=>array(
				array('content-length-range',0,$maxsize),
				array('starts-with','$key',$dir)
			),
		);
	    $base64_policy = base64_encode(json_encode($arr));
	    $signature = base64_encode(hash_hmac('sha1', $base64_policy, self::$accessKeySecret, true));
	    $response = array(
		    'accessid' => self::$accessKeyId,
		    'host' => $host,
		    'policy' => $base64_policy,
		    'signature' => $signature,
		    'expire' => $end,
		    'dir' => $dir
	    );

	    return $response;
    }



    public static function test(){
    	$accessKeyId = "LTAIxhS7vQrxRUoO";
		$accessKeySecret = "TgFP0UA077fjxiU4RHmvtTiNaxyVKX";
		$endpoint = "http://oss-cn-beijing.aliyuncs.com";
		try {
		    $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
		    echo 'fasfsafas';
		} catch (OssException $e) {
		    print $e->getMessage();
		}
    }
}


