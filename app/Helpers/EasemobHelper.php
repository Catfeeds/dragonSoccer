<?php
namespace App\Helpers;
use App\Helpers\CurlHelper;
use App\Models\Systemmsg;
use Config;
use DB;
class EasemobHelper{    
    private static $client_id = 'YXA6h_vEoLVzEee1iP9HlmL6EQ';
    private static $client_secret = 'YXA6oaSOrNewzFNFQfuCq2fUPApQM9k';
    private static $base_url = 'https://a1.easemob.com/1153170905115540/dragonfbonline/';

    //获取app管理员token
    public static function getToken(){  
        $url =  self::$base_url."token"; 
        $body=array(  
        "grant_type"=>"client_credentials",  
        "client_id"=> self::$client_id,  
        "client_secret"=> self::$client_secret 
        );  
        $patoken=json_encode($body);  
        $res = CurlHelper::postCurl($url,$patoken);  
        $tokenResult = array();
        $tokenResult =  json_decode($res, true); 
        return empty($tokenResult["access_token"])?'Authorization:Bearer':"Authorization:Bearer ". $tokenResult["access_token"];  
    }  

    public static function userDetails($username) {  
        $url= self::$base_url."users/".$username;  
        $header = array(Self::getToken());  
        $result = CurlHelper::postCurl($url,'',$header); 
        //var_dump($result);  
        return self::backresult($result);   
    } 

    //注册用户  
    public static function addUser($username='',$password='',$nickname=''){  
        $url= self::$base_url."users/";  
        $body=array(  
            "username"=> $username,  
            "password"=> $password,  
            "nickname"=> $nickname,  
        );  
        $patoken=json_encode($body);  
        $header = array(Self::getToken()); 
        $result = CurlHelper::postCurl($url,$patoken,$header);
        //var_dump($result);  
        //$result =  json_decode($res, true);          
        return self::backresult($result);  
      
    }

    public static function deleteUser($username){  
        $url= self::$base_url."users/".$username; 
        $header = array(Self::getToken()); 
        $result = CurlHelper::postCurl($url,'',$header,'DELETE'); 

        //var_dump($result);  
        return self::backresult($result);     
    }  

    //给用户添加一个好友
    public static function addFriend($owner_username='',$friend_username=''){  
        $url= self::$base_url."users/".$owner_username."/contacts/users/".$friend_username;  
        $header = array(Self::getToken()); 
        $result= CurlHelper::postCurl($url,'',$header);  
        return self::backresult($result);  
    }

    //删除好友
    public static function deleteFriend($owner_username, $friend_username) {  
        $url= self::$base_url."users/" . $owner_username . "/contacts/users/" . $friend_username;  
        $header = array(Self::getToken()); 
        $result= CurlHelper::postCurl($url,'',$header, $type = "DELETE" );  
        return self::backresult($result);  
    }  

    //查看用户的好友
    public static function showFriend($owner_username='') {  
        $url= self::$base_url."users/" . $owner_username . "/contacts/users/";  
        $header = array(Self::getToken());  
        $result = CurlHelper::postCurl ( $url,'', $header,"GET");  
        return self::backresult($result);  
    } 

     /** 
     * 发送消息 
     * 
     * @param string $from_user 
     *          发送方用户名 
     * @param array $username 
     *          array('1','2') 
     * @param string $target_type 
     *          默认为：users 描述：给一个或者多个用户(users)或者群组发送消息(chatgroups) 
     * @param string $content            
     * @param array $ext 
     *          自定义参数 
     */  
    public static function sendMsg($from_user = "", $username = array(), $content = '', $target_type = "users") {
        $option ['target_type'] = $target_type;  
        $option ['target'] = $username;
        $option ['msg'] = array('type'=>'txt','msg'=>$content);  
        $option ['from'] = $from_user;
        $url= self::$base_url."messages";  
        $header = array(Self::getToken());  
        $result = CurlHelper::postCurl ( $url, json_encode($option), $header ); 
        //var_dump($result); 
        return self::backresult($result);  
    }

    /** 
     * 创建群组 
     * 
     * @param $option['groupname'] //群组名称, 
     *          此属性为必须的 
     * @param $option['desc'] //群组描述, 
     *          此属性为必须的 
     * @param $option['public'] //是否是公开群, 
     *          此属性为必须的 true or false 
     * @param $option['approval'] //加入公开群是否需要批准, 
     *          没有这个属性的话默认是true, 此属性为可选的 
     * @param $option['owner'] //群组的管理员, 
     *          此属性为必须的 
     * @param $option['members'] //群组成员,此属性为可选的          
     */  
    public static function createGroups($groupname = '',$desc = '',$owner = '',$members = array(),$public = true ,$approval = false) {  
        $url= self::$base_url."chatgroups"; 

        $option['groupname'] = $groupname;
        $option['desc'] = $desc;
        $option['owner'] = $owner;
        empty($members)?'':$option['members'] = $members;
        $option['public'] = $public;
        $option['approval'] = $approval;

        $header = array(Self::getToken());  
        $result = CurlHelper::postCurl ( $url, json_encode($option), $header ); 
        //var_dump($result); 
        $str = self::backresultContent($result);
        $rlt = json_decode($str,true);
        return empty($rlt['data']['groupid'])?false:$rlt['data']['groupid'];  
    }

    //删除群组
    public static function delGroup($group_id) {  
        $url= self::$base_url."chatgroups" . $group_id;  
        $header = array(Self::getToken());  
        $result = CurlHelper::postCurl ( $url,'', $header,'DELETE'); 
        //var_dump($result); 
        return self::backresult($result); 
    }

    //获取群组详情
    public static function chatGroupsDetails($group_id) {  
        $url= self::$base_url."chatgroups" . $group_id;  
        $header = array(Self::getToken());  
        $result = CurlHelper::postCurl ( $url,'', $header,'GET' ); 
        //var_dump($result); 
        return self::backresult($result); 
    }

    //获取群组成员   
    public static  function groupsUser($group_id) {  
        $url=self::$base_url."chatgroups/" . $group_id . "/users";  
        $header = array(Self::getToken());  
        $result = CurlHelper::postCurl ( $url,'', $header,'GET' ); 
        //var_dump($result); 
        return self::backresult($result); 
    }   

      
    // 群组添加成员
    public static function addGroupsUser($group_id, $usernames = array()) {  
        $url= self::$base_url."chatgroups/" . $group_id . "/users/"; 
        $option['usernames'] = $usernames;
        $header = array(Self::getToken());  
        $result = CurlHelper::postCurl ( $url, json_encode($option), $header );  
        return self::backresult($result);  
    }

    //群组删除 单个成员
    public static function delGroupsUser($group_id, $username) {  
        $url= self::$base_url."chatgroups/" . $group_id . "/users/" . $username;  
        $header = array(Self::getToken());  
        $result = CurlHelper::postCurl ( $url,'', $header,"DELETE");  
        return self::backresult($result);
    } 

   

    
    //返回值处理
    public static function backresult($str){
        //var_dump($str);
        if(gettype($str)=='string'){
            return stripos($str,"error")>0?false:true;
        }

        if(gettype($str)=='array'){
            return stripos(json_encode($str),"error")>0?false:true;
        }

        return false;
    }


    public static function backresultContent($str){
        //var_dump($str);
        if(gettype($str)=='string'){
            return stripos($str,"error")>0?false:$str;
        }

        if(gettype($str)=='array'){
            return stripos(json_encode($str),"error")>0?false:$str;
        }

        return false;
    }

    //发送系统消息
    public static function sendSystemMsgToMember($type,$reciveids, $content) { 
        $systemArr = Config::get('custom.system');
        if($type=='addfriend'){
            $res = true; 
        }else{
            if(count($reciveids)>0){
                $res = false;
                DB::beginTransaction();
                    foreach ($reciveids as $k=>$v) {
                        $insertArr = array();
                        $insertArr['mid'] = substr($v,1);
                        $insertArr['type'] = $type;
                        $insertArr['content'] = $content;
                    }
                    Systemmsg::create($insertArr);
                    $res = true;  
                DB::commit();
            }    
        }
        
        if($res){
            $r = self::addUser($systemArr['easemobArr'][$type],md5($systemArr['easemobArr'][$type]),$systemArr['easemobArr'][$type]); //环信
            $r = self::sendMsg($systemArr['easemobArr'][$type] ,$reciveids,$content); //环信
            return true;    
        }

        return false;
    } 

    //查看用户是否在线
    public function isOnline($username) {
        $url= self::$base_url."dihon/loveofgod/users/" . $username;  
        $header = array(Self::getToken());  
        $result = CurlHelper::postCurl ( $url,'', $header);  
        return self::backresult($result); 
    }

}

?>
