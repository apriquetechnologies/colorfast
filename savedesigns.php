<?php
	require('autoload.php');
	global $lumise;
	$data = $_POST;
	$data['user_id'] = $lumise->connector->get_session('user_id');
	if(!$data['design_id'] || !$data['user_id']){
		echo 'User not logged in';
		return;
	}
	$design_db_id = $lumise->connector->saveDesign($data);
	echo $design_db_id;
?>