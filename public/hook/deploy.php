<?php #!/usr/bin/env /usr/bin/php100dd
    error_reporting(E_ALL);
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
?>
