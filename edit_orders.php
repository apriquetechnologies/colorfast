<?php
require('autoload.php');
global $lumise;
include(theme('header.php'));
$register_result = null;
$is_admin = $lumise->connector->get_session('is_admin') == 'yes' ? 1 : null;
if($is_admin === 1){
if(!empty($_POST)){
	$lumise->connector->approve_orders($_POST['oids'], $is_admin);
}
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
	$items = $lumise->connector->orders($search_filter, $orderby, $ordering, 10, $start, $lumise->connector->get_session('user_id'), 1);
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
<div class="lumise-bread">
    <div class="container">
        <h1><?= $lumise->lang('Edit Orders'); ?></h1> 
    </div>
</div>
<div class="container mt-5">
	<div class="d-flex justify-content-center row">
		<div class="col-md-12">
			<div class="rounded">
				<div class="table-responsive table-borderless">
					<table class="table">
						<thead>
							<tr>
								<th>Order #</th>
								<th>Products</th>
								<th>User</th>
								<th>Approve</th>
								<th>Total</th>
								<th>Created</th>
								<th>Updated</th>
							</tr>
						</thead>
						<tbody class="table-body">
							<?php if(count($items['rows'])==0){ echo '<tr class="cell-1"><td colspan="7" class="text-center">No Pending Orders</td></tr>';}?>
							<?php foreach($items['rows'] as $order){ ?>
							<tr class="cell-1">
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
								<td><input class="form-select approve" type="checkbox" id="<?php echo $order['order_id'];?>"></td>
								<td><?php echo $lumise->lib->price($order['total']);?></td>
								<td><?php echo date('M d, Y', strtotime($order['created']));?></td>
								<td><?php echo date('M d, Y', strtotime($order['updated']));?></td>
							</tr>
							<?php } ?>
							<?php if(count($items['rows'])>0){ echo '<tr class="cell-1"><td colspan="2"><a class="btn" href="/orders.php">Back to Orders</a></td><td colspan="5" class="text-right"><button class="submit btn">Approve</button></td></tr>'; } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<?php } include(theme('footer.php'));?>
<script>
	$(document).ready(function(){
		var oids = [];
		$('.submit').prop('disabled', true);
		$(".approve").change(function() {
			if(this.checked) {
				oids.push($(this).attr('id'));
			}else{
				oids.pop($(this).attr('id'));
			}
			if(oids.length > 0){
				$('.submit').prop('disabled', false);
			}else{
				$('.submit').prop('disabled', true);
			}
			console.log(oids.toString(),oids.length);
		});
		$('.submit').click(function(){
			$('<form>', {
				"id": "approve-orders",
				"method": "POST",
				"html": '<input type="hidden" name="oids" value="' + oids.toString() + '"/>',
				"action": window.location.href
			}).appendTo(document.body).submit();
		});
	});
</script>