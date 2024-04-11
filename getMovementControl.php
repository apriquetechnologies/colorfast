<?php
	require('autoload.php');
	global $lumise;
	$data = $_POST;
	$movement_allowed = $lumise->connector->getProduct($data['product_id']);
	echo $movement_allowed;
?>