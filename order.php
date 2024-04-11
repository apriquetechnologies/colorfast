<?php
require('autoload.php');
global $lumise;
include(theme('header.php'));
$register_result = null;
if($lumise->connector->get_session('user_id')){
	$order_id	= $_GET['oid'];
	$search_filter = array(
		'keyword' => '',
		'fields' => 'order_id,status'
	);
	$orderby  = 'product_id';
	$ordering = 'desc';
	$items = $lumise->connector->products_order($order_id, $search_filter, $orderby, $ordering, $lumise->connector->get_session('user_id'));
}
?>
<style>
.lumise-order-thumbnail {
    max-width: 70px;
    border: 1px solid #ccc;
    margin: 5px;
    padding: 5px;
}
</style>

<div class="lumise-bread" style="margin-bottom:40px;">
    <div class="container">
        <h1><?= $lumise->lang('Order Details'); ?></h1>
    </div>
</div>
<div class="container mt-5">
        <div class="lumise_wrap_table">
			<table class="table">
				<thead>
					<tr>
						<th><?php echo $lumise->lang('Product Name'); ?></th>
						<th><?php echo $lumise->lang('Thumbnail'); ?></th>
						<!-- <th><?php echo $lumise->lang('Attributes'); ?></th> -->
                        <th><?php echo $lumise->lang('Subtotal'); ?></th>
					</tr>
				</thead>
				<tbody>
	                <?php
	                
	                if (count($items['rows']) > 0) {
	                    foreach($items['rows'] as $item):
	                    
	                    $scrs = array();
	                    $pdfid = '';
	                    $sc = @json_decode($item['screenshots']);
						$prt = @json_decode($item['print_files'], true);
						
						$pdfid = $item['cart_id'];
						
						foreach ($sc as $i => $s) {
							array_push($scrs, array(
								"url" => is_array($prt) && isset($prt[$i]) ? $lumise->cfg->upload_url.'orders/'.$prt[$i] : '#',
								"screenshot" => $lumise->cfg->upload_url.'orders/'.$s,
								"download" => true
							));
						}
	                ?>
	                <tr>
						<td><?php echo $item['product_name'] . ' x ' .$item['qty'];?></td>
						<td>
                            <?php
                            $product = $lumise->lib->get_product($item['product_base']);
                            if(isset($item['screenshots']) && $item['screenshots'] != null){
                                $screenshots = json_decode($item['screenshots']);
                                foreach ($screenshots as $screenshot) {
                					echo '<img src="'.$lumise->cfg->upload_url.'orders/'.$screenshot.'" class="lumise-order-thumbnail" />';
                				}
                            }
                            if(isset($item['custom']) && !$item['custom']){
                                
                                if(isset($product['thumbnail_url']))
                                    echo '<img src="'.$product['thumbnail_url'].'" class="lumise-order-thumbnail" />';
                            }
                            ?>
                        </td>
                        <!-- <td></td> -->
                        <td><?php echo $lumise->lib->price($item['product_price']);?></td>
					</tr>
	                    <?php
	                    endforeach;
	                }
	                else {
	                ?>
	                <tr>
	                    <td colspan="6">
	                        <p class="no-data"><?php echo $lumise->lang('Apologies, but no results were found'); ?></p>
	                    </td>
	                </tr>
	                    
	                    
	                <?php
	                }
	                ?>
				</tbody>
                <tfoot class="no-border">
                    <tr>
                        <td colspan="2">
                            <strong style="float: right;"><?php echo $lumise->lang('Order Total:'); ?></strong>
                        </td>
                        <td>
                            <?php echo $lumise->lib->price($items['order']['total']); ?>
                        </td>
                    </tr>
                </tfoot>
			</table>
        </div>
</div>
<?php include(theme('footer.php'));?>