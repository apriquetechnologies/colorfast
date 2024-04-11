<?php
require('autoload.php');
global $lumise;
include(theme('header.php'));
$register_result = null;
$is_admin = $lumise->connector->get_session('is_admin') == 'yes' ? 1 : null;
if($lumise->connector->get_session('user_id')){
	$orderby  = 'order_id';
	$ordering = 'desc';
	$dt_order = 'order_id_desc';

	$current_page = 1;
	$search_filter = array(
		'keyword' => '',
		'fields' => 'order_id,status'
	);

	$start = ( $current_page - 1 ) *  10;
	$items = $lumise->connector->orders($search_filter, $orderby, $ordering, 10, $start, $lumise->connector->get_session('user_id'));
//	echo '<pre>'; print_r($items); die;
}
//echo '<pre>'; 
//print_r($items); die;
?>
<style>
@import url('https://fonts.googleapis.com/css?family=Assistant');
body {
  background: #eee;
  font-family: Assistant, sans-serif;
}
div.dt-buttons {
    float: right !important;
}
.cell-1 {
  border-collapse: separate;
  border-spacing: 0 4em;
  background: #fff;
  border-bottom: 5px solid transparent;
  /*background-color: gold;*/
  background-clip: padding-box;
}

thead {
  background: #dddcdc;
}

.toggle-btn {
  width: 40px;
  height: 21px;
  background: grey;
  border-radius: 50px;
  padding: 3px;
  cursor: pointer;
  -webkit-transition: all 0.3s 0.1s ease-in-out;
  -moz-transition: all 0.3s 0.1s ease-in-out;
  -o-transition: all 0.3s 0.1s ease-in-out;
  transition: all 0.3s 0.1s ease-in-out;
}

.toggle-btn > .inner-circle {
  width: 15px;
  height: 15px;
  background: #fff;
  border-radius: 50%;
  -webkit-transition: all 0.3s 0.1s ease-in-out;
  -moz-transition: all 0.3s 0.1s ease-in-out;
  -o-transition: all 0.3s 0.1s ease-in-out;
  transition: all 0.3s 0.1s ease-in-out;
}

.toggle-btn.active {
  background: blue !important;
}

.toggle-btn.active > .inner-circle {
  margin-left: 19px;
}
</style>
<link rel="stylesheet" href="//cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css"/>
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css"/>
<script src="//cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<div class="lumise-bread">
    <div class="container">
        <h1><?= $lumise->lang('My Orders'); ?></h1> 
		<?php if($is_admin){ ?>
		<div class="form-group mt-2 filter" style="width:50%;margin-right: auto;margin-left: auto;margin-top: 5%;">
			<div class="input-group">
				<div class="input-group-addon">Filter by status</div>
				<select class="form-control">
					<option value="">Show All</option>
					<option value="pending">Pending</option>
					<option value="approved">Approved</option>
					<option value="processing">Processing</option>
					<option value="complete">Complete</option>
					<option value="cancel">Cancelled</option>
				</select>
			</div>
		</div>
		<a class="btn btn-primary" href="/edit_orders.php">Update Pending Orders</a>
		<a class="btn btn-primary" href="/bulk.php">Upload Bulk Orders</a>
		<?php } ?>
    </div>
</div>
<div class="container mt-5">
	<div class="d-flex justify-content-center row">
		<div class="col-md-12 logo">
			<div class="rounded">
				<div class="table-responsive table-borderless">
					<table class="table" id="example">
						<thead>
							<tr>
								<th>Order #</th>
								<th>Products</th>
								<?php if($is_admin){ echo '<th>User</th>'; } ?>
								<th>Status</th>
								<th>Total</th>
								<th>Created</th>
								<th>Updated</th>
							</tr>
						</thead>
						<tbody class="table-body">
							<?php foreach($items['rows'] as $order){ ?>
							<tr class="cell-1 data-tr <?= $order['status']?>">
								<td><a href="/order.php?oid=<?php echo $order['order_id'];?>"><?php printf($lumise->lang('Order #%s'), $order['order_id']);?></a></td>
								<td><?php 
									$products = $lumise->lib->get_order_products($order['order_id']);
									if(count($products)>0){
										$phtml = array();
										foreach($products as $product){
											$phtml[] = $product['product_name'] .' x '.$product['qty'];
										}
										echo implode(', ', $phtml);
									}
									?>
								</td>
								<?php if($is_admin){ echo '<td>'.$items['sub_users'][$order['users_id']]['first_name'].' '.$items['sub_users'][$order['users_id']]['last_name'].'<br/>'.$items['sub_users'][$order['users_id']]['email'].'</td>'; } ?>								
								<td>
									<?php 
										$class = '';
										if (strtolower($order['status']) == 'pending')
											$class = 'pen';
										if (strtolower($order['status']) == 'cancel')
											$class = 'un';
									?>
									<span class="badge badge-success"><?php echo $lumise->apply_filters('order_status', $order['status']);?></span></td>
								<td><?php echo $lumise->lib->price($order['total']);?></td>
								<td><?php echo date('M d, Y', strtotime($order['created']));?></td>
								<td><?php echo date('M d, Y', strtotime($order['updated']));?></td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include(theme('footer.php'));?>
<script>
	$(document).ready(function(){
		var oids = [];
		$(".filter").change(function() {
			var status = $(this).find(":selected").val();
			if(status == ''){
				$('.cell-1').show();
			}else{
				$('.cell-1').hide();
				$('.'+status).show();
			}
		});
		$("#example").DataTable({
			dom: 'l<"clear">BFtrip',
			paging: true,
			autoWidth: true,
			buttons: [
				{ extend: 'excelHtml5', text: 'Download in Excel' }
			],
			initComplete: function (settings, json) {
			}
		});
	});
</script>