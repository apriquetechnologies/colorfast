<?php
	require('autoload.php');
	global $lumise;
	$product_details = $lumise->connector->get_product_details($_GET['id']);
	
	$filename = "attributes_" . date('Y-m-d') . ".csv"; 

	$delimiter = ","; 

	$f = fopen('php://memory', 'w'); 

	$fields = []; 
	foreach($product_details as $key => $attribute){
		$fields[] = $attribute['name'];
	}
	$fields[] = 'quantity';
	
	fputcsv($f, $fields, $delimiter); 
	fseek($f, 0); 

	header('Content-Type: text/csv'); 

	header('Content-Disposition: attachment; filename="' . $filename . '";'); 

	fpassthru($f); 

	exit();

?>