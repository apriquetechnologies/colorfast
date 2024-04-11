<?php
	require('autoload.php');
	global $lumise;
	if($_POST['email'] && $_POST['password']){
		$checkLogin = $lumise->connector->signin();
		if($checkLogin){
			echo true;
		}else{
			echo 'Invalid credentials!';
		}
		return;
	}
	echo 'Please input all fields.';
?>