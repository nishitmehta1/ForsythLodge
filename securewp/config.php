<?php 
	$config = parse_ini_file('config.ini'); 
	$env = $config['env']; // 'DEV' or 'PROD'
	if($env == null)
		$env = 'DEV'; // Default to 'DEV' 
?>