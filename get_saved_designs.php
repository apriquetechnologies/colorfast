<?php
	require('autoload.php');
	global $lumise;
	$user_id = $lumise->connector->get_session('user_id');
	if($user_id){
		$saved_Designs = $lumise->connector->saved_Designs($user_id);
		echo json_encode($saved_Designs);
	}else{
		echo 'error';
	}
?>