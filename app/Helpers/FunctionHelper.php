<?php
namespace App\Helpers;
use App\Helpers\CurlHelper;
class FunctionHelper
{

    /**
     * 验证身份证号
     * @param $vStr
     * @return bool
     */
    public static function isCreditNo($vStr){
        $vCity = array(
            '11','12','13','14','15','21','22',
            '23','31','32','33','34','35','36',
            '37','41','42','43','44','45','46',
            '50','51','52','53','54','61','62',
            '63','64','65','71','81','82','91'
        );
     
        if (!preg_match('/^([\d]{17}[xX\d]|[\d]{15})$/', $vStr)) return false;
     
        if (!in_array(substr($vStr, 0, 2), $vCity)) return false;
     
        $vStr = preg_replace('/[xX]$/i', 'a', $vStr);
        $vLength = strlen($vStr);
     
        if ($vLength == 18){
            $vBirthday = substr($vStr, 6, 4) . '-' . substr($vStr, 10, 2) . '-' . substr($vStr, 12, 2);
        } else {
            $vBirthday = '19' . substr($vStr, 6, 2) . '-' . substr($vStr, 8, 2) . '-' . substr($vStr, 10, 2);
        }
     
        if (date('Y-m-d', strtotime($vBirthday)) != $vBirthday) return false;
        if ($vLength == 18){
            $vSum = 0;     
            for ($i = 17 ; $i >= 0 ; $i--){
                $vSubStr = substr($vStr, 17 - $i, 1);
                $vSum += (pow(2, $i) % 11) * (($vSubStr == 'a') ? 10 : intval($vSubStr , 11));
            }     
            if($vSum % 11 != 1) return false;
        }     
        return true;
        
    }

    public static function getBirthday($vStr){
        if(self::isCreditNo($vStr)){  
            return substr($vStr,6,4).'-'.substr($vStr,10,2).'-'.substr($vStr,12,2);  
        }
        return false;
    }

    public static function getSex($vStr){
        if(self::isCreditNo($vStr)){  
            return (substr($vStr,-2,1)%2=='0')?'m':'f';  
        }
        return false;
    }

    public static function isMobile($mobile){
        if(preg_match("/^1[34578]{1}\d{9}$/",$mobile)){  
            return true;  
        }
        return false;
    }

    public static function makemobilestar($mobile){
        return substr($mobile,0,3).'****'.substr($mobile,-4); ;
    }

    public static function isEmail($email){
        $pattern = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
        if ( preg_match( $pattern, $email ) ){
            return true;   
        }
        return false;
    }
   
    public static function iswap(){
        //var_dump($_SERVER['HTTP_USER_AGENT']);
        if(stripos($_SERVER['HTTP_USER_AGENT'],"android")!=false||stripos($_SERVER['HTTP_USER_AGENT'],"iphone")!=false ||stripos($_SERVER['HTTP_USER_AGENT'],"ipad")!=false ||stripos($_SERVER['HTTP_USER_AGENT'],"ios")!=false||stripos($_SERVER['HTTP_USER_AGENT'],"wp")!=false){
            return true;
        }
        return false;
    }


    public static function isMobileType(){
        //var_dump($_SERVER['HTTP_USER_AGENT']);
        if(stripos($_SERVER['HTTP_USER_AGENT'],"android")!=false){
            return 'a';
        }

        if(stripos($_SERVER['HTTP_USER_AGENT'],"iphone")!=false ||stripos($_SERVER['HTTP_USER_AGENT'],"ipad")!=false ||stripos($_SERVER['HTTP_USER_AGENT'],"ios")!=false ){
            return 'ios';
        }

        return false;
    }

    public static  function makeSn(){
        return date('ymdhis').substr(microtime(),6,4).rand(100,999);
    }

  
	
    // $startime第一个时间int  $endtime第二个时间int 
    public static function timeDifference($startime,$endtime=''){
        empty($endtime)?$endtime=time():'';
        $str = '';
        $tmp = '';
        if($startime>$endtime){
            $tmp = $startime;
            $startime = $endtime;
            $endtime = $tmp;
        }
        //var_dump($endtime-$startime);

        $d=floor(($endtime-$startime)/86400);
        $h=floor(($endtime-$startime)%86400/3600);
        $i=floor(($endtime-$startime)%3600/60);
        //$s=floor(($endtime-$startime)%86400%60);
        empty($d)?'':$str.=$d.'天';  
        empty($h)?$str.='0小时':$str.=$h.'小时';  
        empty($i)?'':$str.=$i.'分钟';  
        //empty($s)?'':$str.=$s.'天';  
        return $str;
    }



    public static function isJson($str){
        if(gettype($str)=='string'){
            return is_object(json_decode($str));
        }
        return false;
    } 


    public static function urlEncodeChinese($url){
        if(gettype($url)=='string'){
            $tempurlArr = parse_url($url);
            $file_url = 'http://'.$tempurlArr['host'].'/'.rawurlencode(substr($tempurlArr['path'],1));
            return $file_url;
        }
        return false;
    }

    public static  function percentEncode($str){
        $res = urlencode($str);
        $res = preg_replace('/\+/', '%20', $res);
        $res = preg_replace('/\*/', '%2A', $res);
        $res = preg_replace('/%7E/', '~', $res);
        return $res;
    }

    public static  function computerAge($birthday){
        $age = strtotime($birthday); 
        if($age === false){ 
            return false; 
        } 
        list($y1,$m1,$d1) = explode("-",date("Y-m-d",$age));       
        list($y2,$m2,$d2) = explode("-",date("Y").'-08-31'); 
        $age = $y2 - $y1; 
        if((int)($m2.$d2) < (int)($m1.$d1)){
            $age -= 1; 
        } 
          
        return $age; 
    }


    //短信发送
    public static  function sendregmsg($mobile,$code){
        if(empty($mobile) || empty($code)){
            return false;
        }

        $url = 'http://dysmsapi.aliyuncs.com/?';
        $tmpid = 'SMS_99970022';   

        $accessKeyId  = 'LTAI06Y1e17ic5GS';
        $accessSecret  = 'oHdUOqpbW1BUIsUafu5I4d48xmkKwH';

        date_default_timezone_set("GMT");

        $data = array(
            'AccessKeyId' =>$accessKeyId,
            'Timestamp' => date('Y-m-d\TH:i:s\Z'), //yyyy-MM-dd’T’HH:mm:ss’Z’
            'SignatureMethod' =>'HMAC-SHA1',
            'SignatureVersion' =>'1.0',
            'SignatureNonce' =>md5(time()),
            'Format' => 'JSON',

            'Action' =>'SendSms',
            'Version' =>'2017-05-25',
            'RegionId' =>'cn-hangzhou',
            'PhoneNumbers' =>$mobile,
            'SignName' =>'龙少足球',
            'TemplateCode' =>$tmpid,
            'TemplateParam' =>json_encode(array('code'=>$code)),
            'OutId' => date('ymdhis')
        );

        date_default_timezone_set("PRC");

        ksort($data);
        $str = '';
        foreach ($data as $k => $v) {
            $str .= '&' . FunctionHelper::percentEncode($k). '=' . FunctionHelper::percentEncode($v);
        }

        $str = substr($str, 1);
        $str2 = 'GET&%2F&'.FunctionHelper::percentEncode($str); 
        $sign = base64_encode(hash_hmac("sha1",$str2, $accessSecret."&",TRUE));
        $url2 = $url.'Signature='.FunctionHelper::percentEncode($sign).'&'.$str;
       
        $rlt = CurlHelper::getdata($url2);

        if(!empty($rlt)){
            try {
                $rltArr = json_decode($rlt,true);
                //var_dump($rltArr);
                //var_dump($rltArr['Code']);
                //var_dump(strtoupper($rltArr['Code'])=='OK');
                if(strtoupper($rltArr['Code'])=='OK'){
                    return true;
                }    
            } catch (\Exception $e) {
                return false;        
            }
            
        }
        return false;
    }

    public static  function checkidcardnum($idcard,$name){
        $url = 'http://op.juhe.cn/idcard/query?key=bc4417eda6de9f5bbd09bea1ea18e06d&idcard='.$idcard.'&realname='.urlencode($name);
        //return true;
        $rlt = CurlHelper::getdata($url);
        //var_dump($rlt);

        if(!empty($rlt)){
            try {
                $rltArr = json_decode($rlt,true);
                if($rltArr['error_code']=='0'){
                    $r = empty($rltArr['result']['res'])?false:$rltArr['result']['res']==1;
                    //var_dump($r);
                    return $r;
                }    
            } catch (\Exception $e) {
                return false;        
            }
            
        }
        return false;    
    }

    //二维数组排序
    public static function arrayRsort($arr=array(),$col = 'success'){
        if(!empty($arr)){
            $arr2 = array();
            foreach ($arr as $k => $v) {
                $arr2[$k] = $v[$col];
            }

            arsort($arr2);

            $arr3 = array();
            foreach ($arr2 as $k2 => $v2) {
                $arr3[] = $arr[$k2];
            }

            return $arr3;
        }
        return false;
    }


    //接口签名
    public static function getSignContent(array $toBeSigned, $verify = false){
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


    //二维数组排序 返回第一列最小值  第二列最大值
    public static function arrayMinMax($arr=array(),$min,$max){
        if(!empty($arr)){
            $arr2 = array();
            $arr3 = array();
            foreach ($arr as $k => $v) {
                $arr2[] = $v[$min];
                $arr3[] = $v[$max];
            }
            asort($arr2);
            arsort($arr3);
            return array('min'=>$arr2[0],'max'=>$arr3[0]);
        }
        return false;
    }

    //级别轮次
    public static function gameLevel($str){
        if(!empty($str)){
            $str = substr($str,0,1).'级第'.substr($str,-1).'轮';
        }
        return $str;
    }
}

?>
