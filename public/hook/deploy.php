<?php #!/usr/bin/env /usr/bin/php100dd
/*    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    set_time_limit(0);
	 
    try {
		    $payload = json_decode(file_get_contents('php://input'), true);
        }
    catch(Exception $e) {
        file_put_contents('/alidata/www/dragonSoccer/logs/github.txt', $e . ' ' . $payload, FILE_APPEND);
        exit(0);
	}
		  
    if ($payload['ref'] === 'refs/heads/master') {
   
        $project_directory = '/alidata/www/dragonSoccer/';
		    
		//$output = shell_exec("/var/www/qadoor/qadoor_site/public/hook/deploy.sh");
		$output = exec("cd /alidata/www/dragonSoccer/ && /usr/bin/git pull");
						     
		//log the request
		file_put_contents('/alidata/www/dragonSoccer/logs/github.txt', $output, FILE_APPEND);
										      
	}
*/
	$secret = "xinyezaici";
	// 项目根目录, 如: "/var/www/fizzday"
	$path = "/alidata/www/dragonSoccer";
	// Headers deliveried from GitHub
	$signature = $_SERVER['HTTP_X_HUB_SIGNATURE'];
	if ($signature) {
	    $hash = "sha1=" . hash_hmac('sha1', file_get_contents("php://input"), $secret);
	    if (strcmp($signature, $hash) == 0) {
	        echo shell_exec("cd {$path} && /usr/bin/git reset --hard origin/master && /usr/bin/git clean -f && /usr/bin/git pull 2>&1");
	        exit();
	    }else{
	    	file_put_contents('/alidata/www/dragonSoccer/logs/github.txt', true);
	    }	
	}else{
	    file_put_contents('/alidata/www/dragonSoccer/logs/github.txt', true);
	}
	http_response_code(404);
?>
