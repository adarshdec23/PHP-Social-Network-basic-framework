<?php
	session_start();
	$_SESSION=array();
	$a=session_destroy();
	if($a){
		header("location: /myam/index.php");		
	}
	else
            echo "Logout error, please manually clear active logins through your browser.";
        exit();
?>
