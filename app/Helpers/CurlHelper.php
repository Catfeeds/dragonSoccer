<?php
namespace App\Helpers;

use GuzzleHttp\Client;

class CurlHelper{

    public static function getdata($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); //get the code of request
        curl_close($ch);

        if($httpCode == 200){
           return $output;
        }

        return false;
    }

    public static function postCurl($url, $body, $header = array(), $method = "POST"){  
        array_push($header, 'Accept:application/json');  
        array_push($header, 'Content-Type:application/json');

        $ch = curl_init();//启动一个curl会话  
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);  
        curl_setopt($ch, CURLOPT_URL, $url);  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 

        curl_setopt($ch, CURLOPT_POST,true);
        switch ($method){   
            case "GET" :   
                curl_setopt($ch, CURLOPT_HTTPGET, true);  
            break;   
            case "POST":   
                   
            break;   
            case "PUT" :   
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");   
            break;   
            case "DELETE":  
                curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, "DELETE");   
            break;   
        }  
          
        curl_setopt($ch, CURLOPT_USERAGENT, 'SSTS Browser/1.0');  
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');  
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);  
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);  //原先是FALSE，可改为2  
        if (isset($body{3}) > 0) {  
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);  
        }  
        if (count($header) > 0) {  
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);  
        }  
      
        $ret = curl_exec($ch);  
        $err = curl_error($ch);
      
        curl_close($ch);
        if ($err) {  
            return $err;  
        }  
      
        return $ret;  
    } 


    //curl请求苹果app_store验证地址
    public static  function http_post_data($url, $data_string){
        $curl_handle=curl_init();
        curl_setopt($curl_handle,CURLOPT_URL, $url);
        curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_handle,CURLOPT_HEADER, 0);
        curl_setopt($curl_handle,CURLOPT_POST, true);
        curl_setopt($curl_handle,CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($curl_handle,CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl_handle,CURLOPT_SSL_VERIFYPEER, 0);
        $response_json =curl_exec($curl_handle);        
        curl_close($curl_handle);
        return $response_json;
    }

    //ali
    public static  function curl_ali($url, $data_string) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data_string);
        $headers = array('content-type: application/x-www-form-urlencoded;charset=UTF-8');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $output = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if($httpCode == 200){
           return $output;
        }

        return false;
    }


    //wechat
    public static  function curl_wechat($url, $vars, $second=30){
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_TIMEOUT,$second);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);

        curl_setopt($ch,CURLOPT_SSLCERT,dirname(__FILE__)."/apiclient_cert.pem");
        curl_setopt($ch,CURLOPT_SSLKEY,dirname(__FILE__)."/apiclient_key.pem");
        //curl_setopt($ch,CURLOPT_CAINFO,'/rootca.pem'); 
     
        curl_setopt($ch,CURLOPT_POST, 1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$vars);

        $output = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if($httpCode == 200){
           return $output;
        }

        return false;
    }

}
