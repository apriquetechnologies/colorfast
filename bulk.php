<?php
	require('autoload.php');
	global $lumise;
	$db			= $lumise->get_db();
	$date		= @date ("Y-m-d H:i:s");
	$total_orders	= 0;
	$upload_status	= isset($_GET['total']) ? "<b>Total bulk orders created</b> : ".$_GET['total'] : '';
	if(!empty($_FILES) && $_POST['product_id'] != ''){
		$product_id		 = $_POST['product_id'];
		$product_details = $lumise->connector->getProduct($product_id,1);

		$attributes		 = $lumise->lib->dejson($product_details['attributes'],1);

		$product_attributes	 = [];
		foreach($attributes as $att){
			$product_attributes[$att['name']]['id']	   = $att['id'];
			$product_attributes[$att['name']]['name']  = $att['name'];
			$product_attributes[$att['name']]['value'] = '';
		}

		$order_data = array(
			'total'		=> 1,
			'status'	=> 'pending',
			'currency'	=> $lumise->cfg->settings['currency'],
			'users_id'	=> $lumise->connector->get_session('user_id'),		// Id from cf_users table
			'user_id'	=> $lumise->connector->get_session('user_id'),
			'user_id_shipping' => null,
			'payment'	=> 'Bulk',
			'txn_id'	=> '',
			'gst'		=> null,
			'created'	=> $date,
			'updated'	=> $date,
			'bulk'		=> 1
		);
		$order_product_data = array(
			'product_base'	=> $product_details['id'],
			'product_id'	=> $product_details['id'],
			'cart_id'		=> 'Bulk',
			'data' => array(
				'printing'		=> '',
				'variation'		=> ''
			),
			'screenshots'	=> '',
			'print_files'	=> '',
			'created'		=> $date,
			'updated'		=> $date,
			'product_price' => $product_details['price'],
			'product_name'	=> $product_details['name'],
			'currency'		=> $order_data['currency'],
			'qty'			=> 1,			// Modify Later
			'design'		=> '',
			'custom'		=> '',
			'author'		=> ''
		);

		$csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');
		if (!empty($_FILES['csvFile']['name']) && in_array($_FILES['csvFile']['type'], $csvMimes)) {
			if (is_uploaded_file($_FILES['csvFile']['tmp_name'])) {
				// Create new order
				$new_order_id = $db->insert ('orders', $order_data);
				$order_product_data['order_id']				= $new_order_id;

				$csvFile = fopen($_FILES['csvFile']['tmp_name'], 'r');
				
				// Get headers of CSV file
				$headers = fgetcsv($csvFile);
				// Iterate through the data in csv file
				while (($line = fgetcsv($csvFile)) !== false) {
					// Make an array per attribute
					$new_pro_att = [];
					$new_opd= $order_product_data;

					foreach($product_attributes as $name => $data){
						if(in_array($name, $headers)){
							$data['id']		= $data['id'];
							$data['value']	= $line[array_search($name, $headers)];
							$data['name']	= $data['name'];
							$new_pro_att[$data['id']]	= $data;
						}
					}
					$new_pro_att['quantity']['value']	= $line[array_search('quantity', $headers)];
					$new_pro_att['quantity']['id']		= 'quantity';
					$new_pro_att['quantity']['name']	= 'Quantity';
					$new_opd['data']['attributes']	= $new_pro_att;
					$enjsonData	=	$new_opd['data'];
					$new_opd['data']					= $lumise->lib->enjson($enjsonData);
					$db->insert('order_products', $new_opd);
					$total_orders++;
				}
				$db->where ('id', $new_order_id)->update ('orders', ['total'=>$product_details['price']*$total_orders]);
				fclose($csvFile);
				header("location:/bulk.php?total=$total_orders");
			}
		}else{
		}
	}else{
		include(theme('header.php'));
		$search_filter = ['keyword' => '', 'fields' => 'name'];
		$products = $lumise->lib->get_rows('products', $search_filter, "`name`", 'asc', 100, 0, array('active'=> 1), "", 1)['rows'];
?>
<div class="lumise-bread">
    <div class="container">
        <h1><?= $lumise->lang('Bulk Orders'); ?></h1> 
    </div>
</div>
<form action="bulk.php" method="post" class="lumise_form" id="checkoutform" enctype="multipart/form-data">
	<div class="container">
		<div class="row">
			<div id="checkout" class="padding6 span12">
				<div class="col-md-6 billing col-md-offset-3 container-form text-center">
					<div class="control-group col-sm-6 col-sm-offset-3">
						<h5 class="text-center" style="margin-bottom:10px"><?= $upload_status; ?></h3>
						<div class="mb-3">
							<select class="form-select" name="product_id" id="products">
								<option selected value=''>Select Product</option>
								<?php foreach($products as $product){ 
									echo '<option value="'.$product['id'].'">'.$product['name'].'</option>';
								} ?>
							</select>
							<small><a class="btn btn-sm add-new link lumise-button download-bulk-csv"><i class="fa fa-download"></i> Download Bulk CSV Template</a></small>
						</div>
						<div class="mb-3" style="margin-top:10px">
							<input type="file" name="csvFile" accept=".xlsx, .xls, .csv" class="lumise-file-upload" id="upload_file_upload">
						</div>					
						<div class="controls h6">
							<button name="submit" type="submit" class="submit_btn btn btn-large btn-primary">Submit</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>
<script>
	$(document).ready(function (){
		$(document).on('click', '.download-bulk-csv', function(){ 
			var product_id = $('#products').find(":selected").val();
			if(product_id != ''){
				window.location = 'download_csv.php?id='+product_id;
			}else{
				alert('Please select a product');
			}
		});
	});
</script>
<?php } include(theme('footer.php'));?>